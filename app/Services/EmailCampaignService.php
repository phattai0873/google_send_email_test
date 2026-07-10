<?php

namespace App\Services;

use App\Jobs\SendCampaignJob;
use App\Models\EmailLog;
use App\Models\User;

class EmailCampaignService
{
    /**
     * Send email campaign to recipients and log the execution via queue.
     *
     * @param User $user
     * @param array $data
     * @return EmailLog
     */
    public function sendCampaign(User $user, array $data): EmailLog
    {
        $recipientsData = [];
        foreach ($data['recipients'] as $recipient) {
            $recipientsData[] = [
                'email' => $recipient,
                'status' => 'pending',
                'error' => null,
            ];
        }

        $log = EmailLog::create([
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'content' => $data['content'],
            'total_recipients' => count($data['recipients']),
            'sent_success' => 0,
            'sent_failed' => 0,
            'recipients' => $recipientsData,
            'status' => 'pending',
        ]);

        // Dispatch asynchronous queue job
        SendCampaignJob::dispatch($log);

        return $log;
    }
}
