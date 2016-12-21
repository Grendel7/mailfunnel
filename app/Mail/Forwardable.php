<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Swift_Message;

class Forwardable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string The HTML content of the message, if defined
     */
    protected $html;

    /**
     * @var string The plain text content of the message, if defined
     */
    protected $text;

    /**
     * @var string The name of the provider this message was received from
     */
    protected $provider;

    /**
     * @var array Additional headers in this message
     */
    protected $headers = [];

    /**
     * Forwardable constructor.
     * @param string $provider An identifier for the provider issuing this message
     */
    public function __construct($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Set the HTML content of the message
     *
     * @param string $html
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Set the plain text content of the message
     *
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build()
    {
        $headers = $this->headers;
        $this->withSwiftMessage(function(Swift_Message $swiftMessage) use ($headers) {
            $headerSet = $swiftMessage->getHeaders();

            foreach($headers as list($key, $value)) {
                $headerSet->addTextHeader($key, $value);
            };
        });

        if ($this->html && $this->text) {
            $view = $this->view('email.html')->text('email.text');
        } elseif ($this->html) {
            $view = $this->view('email.html');
        } else {
            $view = $this->text('email.text');
        }

        return $view->with([
            'html' => $this->html,
            'text' => $this->text
        ]);
    }

    /**
     * Add a custom e-mail header
     *
     * @param string $name The key of the header
     * @param string $value The value of the header
     */
    public function addHeader($name, $value)
    {
        if (in_array($name, $this->getTransformableHeaders())) {
            $this->headers[] = ['X-Original-'.$name, $value];
        } elseif (!in_array($name, $this->getIgnoredHeaders())) {
            $this->headers[] = [$name, $value];
        }
    }

    /**
     * Get the HTML view name
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Get the plain text view name
     *
     * @return string
     */
    public function getTextView()
    {
        return $this->textView;
    }

    /**
     * Get the view data
     *
     * @return array
     */
    public function getViewData()
    {
        return $this->viewData;
    }

    /**
     * Get the forwarded headers which should not be included as-is
     *
     * @return array
     */
    protected function getIgnoredHeaders()
    {
        return ['Subject', 'From', 'To', 'Content-Type', 'Mime-Version'];
    }

    /**
     * Get the headers which should be forwarded but with new names
     *
     * @return array
     */
    protected function getTransformableHeaders()
    {
        return ['Message-ID', 'Date', 'Reply-To', 'DKIM-Signature', 'Sender'];
    }
}
