<?php

namespace App\Mail;

use App\Models\Address;
use App\Models\Message;
use App\ReplyEmail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

class InboundMail extends Forwardable
{
    /**
     * @var string The address the message was sent to
     */
    protected $originalTo;

    /**
     * @var string The address the message was sent from
     */
    protected $originalFrom;

    /**
     * @var float The spam score of the message
     */
    protected $spamScore;

    /**
     * Set the original to string: You <test@example.com>
     *
     * @param $originalTo
     * @return $this
     */
    public function setOriginalTo($originalTo)
    {
        $this->originalTo = $originalTo;

        return $this;
    }

    /**
     * Set original from string: Bob <bob@example.com>
     *
     * @param $originalFrom
     * @return $this
     */
    public function setOriginalFrom($originalFrom)
    {
        $this->originalFrom = $originalFrom;

        return $this;
    }

    public function setSpamScore($spamScore)
    {
        $this->spamScore = $spamScore;

        return $this;
    }

    /**
     * Return the original from address safe for inclusion in headers: Bob 'bob@example.com'
     *
     * @return string
     */
    protected function getSafeOriginalFrom()
    {
        return str_replace(['<', '>'], "'", $this->originalFrom);
    }

    /**
     * Get the e-mail address this message was sent to: test@example.com
     *
     * @return string
     */
    protected function getOriginalToEmail()
    {
        $matches = [];
        if (preg_match('/<(.*)>/', $this->originalTo, $matches)) {
            return $matches[1];
        } else {
            return $this->originalTo;
        }
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build()
    {
        return parent::build()
            ->replyTo(ReplyEmail::generate($this->originalTo, $this->originalFrom))
            ->to(config('mailfunnel.recipient.email'), config('mailfunnel.recipient.name'))
            ->from(config('mail.from.address'), $this->getSafeOriginalFrom() . " via " . $this->getOriginalToEmail());
    }

    /**
     * Prepare the message for sending
     *
     * @param $all array The full request data
     * @return bool
     */
    public function validate($all)
    {
        $address = Address::firstOrCreate([
            'email' => $this->getOriginalToEmail(),
        ]);

        if ($address->is_blocked) {
            $this->saveEmail($address,
                \App\Models\Message::STATUS_REJECTED_LOCAL,
                \App\Models\Message::REASON_ADDRESS_BLOCKED
            );

            return false;
        }

        Log::info('Received message for provider '.$this->provider, $all);

        if ($this->spamScore >= config($this->provider.'.spamassassin_score')) {
            $this->saveEmail($address,
                Message::STATUS_REJECTED_LOCAL,
                Message::REASON_SPAM_SCORE
            );
            return false;
        }

        return $this->saveEmail($address, Message::STATUS_SENT);
    }

    /**
     * Save a message to the database
     *
     * @param Address $address
     * @param $status
     * @param $reason
     * @return bool
     */
    protected function saveEmail(Address $address, $status, $reason = '')
    {
        $model = new Message([
            'from' => $this->originalFrom,
            'subject' => $this->subject,
            'status' => $status,
            'reason' => $reason,
            'spam_score' => $this->spamScore ? $this->spamScore : 0.0,
            'address_id' => $address->id,
        ]);

        return $model->save();
    }
}