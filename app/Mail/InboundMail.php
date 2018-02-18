<?php

namespace App\Mail;

use App\Models\Address;
use App\Models\Domain;
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

    /**
     * Set the spam score reported by the upstream
     *
     * @param float $spamScore
     * @return $this
     */
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
    public function getSafeOriginalFrom()
    {
        return str_replace(['<', '>'], "'", $this->originalFrom);
    }

    /**
     * Get the e-mail address this message was sent to: test@example.com
     *
     * @return string
     */
    public function getOriginalToEmail()
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
        $domain = Domain::where('domain', explode('@', $this->getOriginalToEmail())[1])->first();

        return parent::build()
            ->replyTo(ReplyEmail::generate($this->originalTo, $this->originalFrom))
            ->to($domain->user->email, $domain->user->name)
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
        $domain = Domain::where('domain', explode('@', $this->getOriginalToEmail())[1])->first();

        if (!$domain) {
            Log::warning('The domain name is not registered!', [
                'domain' => $domain,
                'originalToEmail' => $this->getOriginalToEmail(),
                'data' => $all,
            ]);

            return false;
        }

        $address = Address::firstOrCreate(['email' => $this->getOriginalToEmail()], ['domain_id' => $domain->id]);

        if ($address->is_blocked) {
            $this->saveEmail($address, true, \App\Models\Message::REASON_ADDRESS_BLOCKED);

            return false;
        }

        Log::info('Received message for provider '.$this->provider, $all);

        if ($this->spamScore >= config($this->provider.'.spamassassin_score', 5)) {
            $this->saveEmail($address, true, Message::REASON_SPAM_SCORE);

            return false;
        }

        return $this->saveEmail($address);
    }

    /**
     * Save a message to the database
     *
     * @param Address $address
     * @param boolean $rejected
     * @param $reason
     * @return bool
     */
    protected function saveEmail(Address $address, $rejected = false, $reason = '')
    {
        $model = new Message([
            'from' => $this->originalFrom,
            'subject' => $this->subject,
            'is_rejected' => $rejected,
            'reason' => $reason,
            'spam_score' => $this->spamScore ? $this->spamScore : 0.0,
            'address_id' => $address->id,
        ]);

        return $model->save();
    }
}
