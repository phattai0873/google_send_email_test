<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subject',
        'content',
        'total_recipients',
        'sent_success',
        'sent_failed',
        'recipients',
        'status',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recipients' => 'array',
        ];
    }

    /**
     * Get the user who sent the emails.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
