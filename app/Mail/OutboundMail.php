<?php

namespace App\Mail;

use App\ReplyEmail;
use Illuminate\Mail\Mailable;

class OutboundMail extends Forwardable
{
    /**
     * @var ReplyEmail
     */
    protected $replyEmail;

    /**
     * Set the to address this message was sent to
     *
     * @param string $email
     */
    public function setReplyEmail($email)
    {
        $this->replyEmail = new ReplyEmail($email);
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build()
    {
        return parent::build()
            ->from($this->replyEmail->getFromEmail(), $this->replyEmail->getFromName())
            ->to($this->replyEmail->getToEmail(), $this->replyEmail->getToName());
    }
}
