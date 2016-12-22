<?php

namespace App\Http\Controllers;

use App\Mail\InboundMail;
use App\Mail\OutboundMail;
use App\ReplyEmail;
use Illuminate\Http\Request;

class SendgridController extends Controller
{
    /**
     * Process an inbound e-mail from Sendgrid
     *
     * @param Request $request
     * @return string
     */
    public function inbound(Request $request)
    {
        $mail = new InboundMail('sendgrid');
        $mail->setSpamScore($request->input('spam_score', 0));
        $mail->setHtml($request->input('html'));
        $mail->setText($request->input('text'));
        $mail->subject($request->input('subject'));
        $mail->setOriginalTo($request->input('to'));
        $mail->setOriginalFrom($request->input('from'));

        foreach ($this->getAttachments($request) as $attachment) {
            $mail->attach($attachment->getRealPath(), [
                'as' => $attachment->getClientOriginalName(),
                'mime' => $attachment->getClientMimeType()
            ]);
        }

        foreach (explode('\n', $request->input('headers')) as $header) {
            list($key, $value) = array_map('trim', explode(':', $header, 2));

            $mail->addHeader($key, $value);
        }

        if ($mail->validate($request->all())) {
            $this->app->mailer->send($mail);
        }

        return response('SUCCESS');
    }

    /**
     * Process an outbound e-mail from Sendgrid
     *
     * @param Request $request
     * @return string
     */
    public function outbound(Request $request)
    {
        if (!ReplyEmail::isAuthorized(json_decode($request->input('envelope'), true)['from'])) {
            return response('UNAUTHORIZED');
        }

        $mail = new OutboundMail('sendgrid');
        $mail->setHtml($request->input('html'));
        $mail->setText($request->input('text'));
        $mail->subject($request->input('subject'));
        $mail->setReplyEmail(json_decode($request->input('envelope'), true)['to']);

        foreach ($this->getAttachments($request) as $attachment) {
            $mail->attach($attachment->getRealPath(), [
                'as' => $attachment->getClientOriginalName(),
                'mime' => $attachment->getClientMimeType()
            ]);
        }

        $this->app->mailer->send($mail);

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
        $attachments = json_decode($request->get('attachment-info', '{}'), true);
        $result = [];

        foreach ($attachments as $key => $attachment) {
            $result[] = $request->file($key);
        }

        return $result;
    }
}