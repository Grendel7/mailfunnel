<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    const REASON_ADDRESS_BLOCKED = 'address_blocked';
    const REASON_SPAM_SCORE = 'spam_score';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from', 'subject', 'is_rejected', 'reason', 'spam_score', 'address_id'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['address'];

    /**
     * Get the address this message was sent to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }


    public function getIsRejectedAttribute($value)
    {
        return (boolean) $value;
    }
}
