<?php

namespace App\Jobs;

use App\Mail\CampaignEmail;
use App\Models\EmailLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailLog;

    /**
     * Create a new job instance.
     *
     * @param EmailLog $emailLog
     */
    public function __construct(EmailLog $emailLog)
    {
        $this->emailLog = $emailLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $log = $this->emailLog;
        $user = $log->user;

        if (!$user) {
            $log->update(['status' => 'failed']);
            return;
        }

        $log->update(['status' => 'sending']);

        $recipientsData = $log->recipients;
        $sentSuccess = 0;
        $sentFailed = 0;

        $htmlContent = view('emails.campaign', [
            'emailSubject' => $log->subject,
            'content' => $log->content
        ])->render();

        foreach ($recipientsData as $index => &$recipientItem) {
            $recipient = is_array($recipientItem) ? $recipientItem['email'] : $recipientItem;
            $sendOk = false;
            $errorMsg = null;

            if (!empty($user->google_token)) {
                try {
                    $encodedName = "=?utf-8?B?" . base64_encode($user->name) . "?=";
                    $rawMessage = "From: " . $encodedName . " <" . $user->email . ">\r\n";
                    $rawMessage .= "To: " . $recipient . "\r\n";
                    $rawMessage .= "Subject: =?utf-8?B?" . base64_encode($log->subject) . "?=\r\n";
                    $rawMessage .= "MIME-Version: 1.0\r\n";
                    $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
                    $rawMessage .= $htmlContent;

                    $base64Message = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($rawMessage));

                    $response = Http::withToken($user->google_token)->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/send", [
                        'raw' => $base64Message
                    ]);

                    if ($response->successful()) {
                        $sendOk = true;
                    } else {
                        $errorMsg = "Gmail API: " . $response->status() . " " . $response->body();
                        logger()->error("Gmail API send failed to {$recipient}: " . $response->body());
                    }
                } catch (Exception $e) {
                    $errorMsg = $e->getMessage();
                    logger()->error("Gmail API exception for {$recipient}: " . $e->getMessage());
                }
            } else {
                // Fallback to default Mail SMTP (e.g. for testing/mock users)
                try {
                    Mail::to($recipient)->send(new CampaignEmail($log->subject, $log->content));
                    $sendOk = true;
                } catch (Exception $e) {
                    $errorMsg = $e->getMessage();
                    logger()->error("SMTP Fallback send failed to {$recipient}: " . $e->getMessage());
                }
            }

            if ($sendOk) {
                $sentSuccess++;
                if (is_array($recipientItem)) {
                    $recipientItem['status'] = 'success';
                }
            } else {
                $sentFailed++;
                if (is_array($recipientItem)) {
                    $recipientItem['status'] = 'failed';
                    $recipientItem['error'] = $errorMsg;
                }
            }

            // Update log metrics dynamically after sending each email
            $log->update([
                'sent_success' => $sentSuccess,
                'sent_failed' => $sentFailed,
                'recipients' => $recipientsData,
            ]);

            // Throttle the sending to avoid triggering spam filters (delay 1 second per mail)
            sleep(1);
        }

        $log->update([
            'status' => $sentFailed === 0 ? 'completed' : ($sentSuccess === 0 ? 'failed' : 'completed')
        ]);
    }
}
