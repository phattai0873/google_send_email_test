<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\CampaignEmail;
use App\Models\EmailLog;
use Exception;

class EmailCampaignController extends Controller
{
    /**
     * Display the campaign page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $logs = [];

        if (Auth::check()) {
            // Load email logs with user information for the authenticated user
            $logs = EmailLog::where('user_id', Auth::id())
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('welcome', compact('user', 'logs'));
    }

    /**
     * Send campaign email to recipients.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('home')->with('error', 'Bạn cần đăng nhập để thực hiện chức năng này.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|email',
        ], [
            'subject.required' => 'Tiêu đề email không được để trống.',
            'subject.max' => 'Tiêu đề email không được vượt quá 255 ký tự.',
            'content.required' => 'Nội dung email không được để trống.',
            'recipients.required' => 'Danh sách người nhận không được để trống.',
            'recipients.min' => 'Bạn phải chọn ít nhất một người nhận.',
            'recipients.*.required' => 'Email không được để trống.',
            'recipients.*.email' => 'Địa chỉ email không hợp lệ.',
        ]);

        $recipients = $request->recipients;

        $sentSuccess = 0;
        $sentFailed = 0;

        $user = Auth::user();
        $htmlContent = view('emails.campaign', [
            'emailSubject' => $request->subject,
            'content' => $request->content
        ])->render();

        foreach ($recipients as $recipient) {
            if (!empty($user->google_token)) {
                // Send dynamically using Google Gmail API with the user's logged-in token
                try {
                    $encodedName = "=?utf-8?B?" . base64_encode($user->name) . "?=";
                    $rawMessage = "From: " . $encodedName . " <" . $user->email . ">\r\n";
                    $rawMessage .= "To: " . $recipient . "\r\n";
                    $rawMessage .= "Subject: =?utf-8?B?" . base64_encode($request->subject) . "?=\r\n";
                    $rawMessage .= "MIME-Version: 1.0\r\n";
                    $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
                    $rawMessage .= $htmlContent;

                    $base64Message = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($rawMessage));

                    $response = Http::withToken($user->google_token)->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/send", [
                        'raw' => $base64Message
                    ]);

                    if ($response->successful()) {
                        $sentSuccess++;
                    } else {
                        logger()->error("Gmail API send failed to {$recipient}: " . $response->body());
                        $sentFailed++;
                    }
                } catch (Exception $e) {
                    logger()->error("Gmail API exception for {$recipient}: " . $e->getMessage());
                    $sentFailed++;
                }
            } else {
                // Fallback to default Mail SMTP (e.g. for testing/mock users)
                try {
                    Mail::to($recipient)->send(new CampaignEmail($request->subject, $request->content));
                    $sentSuccess++;
                } catch (Exception $e) {
                    logger()->error("SMTP Fallback send failed to {$recipient}: " . $e->getMessage());
                    $sentFailed++;
                }
            }
        }

        // Save email log details
        EmailLog::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'content' => $request->content,
            'total_recipients' => count($recipients),
            'sent_success' => $sentSuccess,
            'sent_failed' => $sentFailed,
            'recipients' => $recipients,
        ]);

        if ($sentFailed === 0) {
            return redirect()->route('home')->with('success', "Gửi chiến dịch email thành công! Đã gửi thành công {$sentSuccess} email.");
        } elseif ($sentSuccess === 0) {
            return redirect()->route('home')->with('error', "Gửi chiến dịch email thất bại! Không thể gửi được email nào (Thất bại: {$sentFailed}).");
        } else {
            return redirect()->route('home')->with('info', "Chiến dịch hoàn thành một phần. Thành công: {$sentSuccess}, Thất bại: {$sentFailed}.");
        }
    }
}
