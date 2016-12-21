<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const STATUS_REJECTED_LOCAL = 'rejected_local';
    const STATUS_SENT = 'sent';

    const REASON_ADDRESS_BLOCKED = 'address_blocked';
    const REASON_SPAM_SCORE = 'spam_score';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from', 'subject', 'status', 'reason', 'spam_score', 'address_id'];
}