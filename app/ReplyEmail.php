<?php

namespace App;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ReplyEmail
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Create a new ReplyEmail instance
     *
     * @param string $address The e-mail address an outbound message was sent to
     */
    public function __construct($address)
    {
        $this->data = json_decode(base64_decode(explode('@', $address)[0]), true);

        $this->data['from'] = $this->extractAddress($this->data['from']);
        $this->data['to'] = $this->extractAddress($this->data['to']);
    }

    /**
     * Split an address into a name and e-mail (if required)
     *
     * @param string|array $address
     * @return array
     */
    public function extractAddress($address)
    {
        if (!is_string($address)) {
            return $address;
        }

        if (strpos($address, '<') === false) {
            return [
                'name' => null,
                'email' => $address,
            ];
        }

        $matches = [];
        preg_match('/(.+)\s+<([\w@\.]+)>/', $address, $matches);

        return [
            'name' => $matches[1],
            'email' => $matches[2],
        ];
    }

    /**
     * Generate a reply address
     *
     * @param string $from John Doe <test@example.com>
     * @param string $to John Doe <test@example.com>
     * @return string
     */
    public static function generate($from, $to)
    {
        return base64_encode(json_encode([
                'from' => $from,
                'to' => $to,
            ])) . '@' . config('mailfunnel.reply_domain');
    }

    /**
     * Check if an e-mail address is authorized to send a reply e-mail
     *
     * @param string $sender A reply e-mail address
     * @return bool
     */
    public static function isAuthorized($sender)
    {
        $authorized = Arr::first(config('app.recipients'), function ($key, $value) use ($sender) {
            list($name, $email) = $value;
            return $sender === $email;
        });

        if ($authorized) {
            return true;
        } else {
            Log::warning('Sender is not authorized', ['sender' => $sender]);
        }
    }

    /**
     * Get the from e-mail from the reply address
     *
     * @return string
     */
    public function getFromEmail()
    {
        return $this->data['from']['email'];
    }

    /**
     * Get the from name from the reply address
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->data['from']['name'];
    }

    /**
     * Get the to e-mail from the reply address
     *
     * @return string
     */
    public function getToEmail()
    {
        return $this->data['to']['email'];
    }

    /**
     * Get the to name from the reply address
     *
     * @return string
     */
    public function getToName()
    {
        return $this->data['to']['name'];
    }
}