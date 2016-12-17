<?php

namespace App\Http\Controllers;

use App\Mail\InboundMail;
use App\Mail\OutboundMail;
use App\ReplyEmail;
use Illuminate\Http\Request;

class MailgunController extends Controller
{

    /**
     * Process an inbound e-mail from Mandrill
     *
     * @param Request $request
     * @return string
     */
    public function inbound(Request $request)
    {
        $headers = json_decode($request->get('message-headers', '[]'));

        $mail = new InboundMail('mailgun');
        $mail->setSpamScore(array_first($headers, function($header) { return $header[0] == 'X-Mailgun-Sscore'; }));
        $mail->setHtml($request->get('body-html'));
        $mail->setText($request->get('body-plain'));
        $mail->subject($request->get('subject'));
        $mail->setOriginalTo($request->get('to'));
        $mail->setOriginalFrom($request->get('from'));

        foreach ($this->getAttachments($request) as $attachment) {
            $mail->attach($attachment->getRealPath(), [
                'as' => $attachment->getClientOriginalName(),
                'mime' => $attachment->getClientMimeType()
            ]);
        }

        foreach ($headers as list($key, $value)) {
            $mail->addHeader($key, $value);
        }

        if ($mail->validate($request->all())) {
            $this->app->mail->send($mail);
            return response('SUCCESS');
        } else {
            return response('ERROR', 422);
        }
    }

    /**
     * Process an outbound e-mail from Mandrill
     *
     * @param Request $request
     * @return string
     */
    public function outbound(Request $request)
    {
        if (!ReplyEmail::isAuthorized($request->get('sender'))) {
            return response('UNAUTHORIZED', 406);
        }

        $mail = new OutboundMail('mailgun');
        $mail->setHtml($request->get('body-html'));
        $mail->setText($request->get('body-plain'));
        $mail->subject($request->get('subject'));
        $mail->setReplyEmail($request->get('recipient'));

        foreach ($this->getAttachments($request) as $attachment) {
            $mail->attach($attachment->getRealPath(), [
                'as' => $attachment->getClientOriginalName(),
                'mime' => $attachment->getClientMimeType()
            ]);
        }

        $this->app->mail->send($mail);

        return response('SUCCESS');
    }

    /**
     * Get a list of attachments connected to this e-mail
     *
     * @param Request $request
     * @return \Illuminate\Http\UploadedFile[]
     */
    protected function getAttachments(Request $request)
    {
        $count = $request->get('attachment-count', 0);

        $attachments = [];
        for ($i = 1; $i <= $count; $i++) {
            $attachments[] = $request->file('attachment-' . $i);
        }

        return $attachments;
    }
}