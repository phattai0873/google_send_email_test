<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EmailCampaignService;
use App\Models\EmailLog;

class EmailCampaignController extends Controller
{
    protected $campaignService;

    /**
     * Create a new controller instance.
     *
     * @param EmailCampaignService $campaignService
     */
    public function __construct(EmailCampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * Display the campaign page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        return view('welcome', compact('user'));
    }

    /**
     * Display the email logs history page.
     *
     * @return \Illuminate\View\View
     */
    public function logs()
    {
        $user = Auth::user();
        $logs = EmailLog::where('user_id', Auth::id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('logs', compact('user', 'logs'));
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

        $validated = $request->validate([
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

        $log = $this->campaignService->sendCampaign(Auth::user(), $validated);
        $count = count($validated['recipients']);

        return redirect()->route('email.logs')->with('success', "Chiến dịch gửi email đã được khởi tạo! Hệ thống đang tiến hành gửi ngầm tới {$count} người nhận.");
    }
}
