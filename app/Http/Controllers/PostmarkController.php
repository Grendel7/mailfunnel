<?php

namespace App\Http\Controllers;

use App\Mail\InboundMail;
use App\Mail\OutboundMail;
use App\ReplyEmail;
use Illuminate\Http\Request;

class PostmarkController extends Controller
{
    /**
     * Process an inbound e-mail from Postmark
     *
     * @param Request $request
     * @return string
     */
    public function inbound(Request $request)
    {
        $mail = new InboundMail('postmark');
        $mail->setSpamScore(array_first($request->get('Headers', []), function($header) {
            return $header['Name'] == 'X-Spam-Score';
        })['Value']);
        $mail->setHtml(html_entity_decode($request->get('HtmlBody')));
        $mail->setText($request->get('TextBody'));
        $mail->subject($request->get('Subject'));
        $mail->setOriginalTo($request->get('To'));

        if (!empty($request->get('FromName'))) {
            $mail->setOriginalFrom("{$request->get('FromName')} <{$request->get('From')}>");
        } else {
            $mail->setOriginalFrom($request->get('From'));
        }

        foreach ($this->getAttachments($request) as $attachment) {
            $mail->attachData($attachment['data'], $attachment['name']);
        }

        foreach($request->get('Headers', []) as $header) {
            $mail->addHeader($header['Name'], $header['Value']);
        }

        if ($mail->validate($request->all())) {
            $this->app->mail->send($mail);
            return response('SUCCESS');
        } else {
            return response('ERROR', 422);
        }
    }

    /**
     * Process an outbound e-mail from Postmark
     *
     * @param Request $request
     * @return string
     */
    public function postOutbound(Request $request)
    {
        if (!ReplyEmail::isAuthorized($request->get('FromFull')['Email'])) {
            return response('UNAUTHORIZED', 422);
        }

        $mail = new OutboundMail('postmark');
        $mail->setHtml(html_entity_decode($request->get('HtmlBody')));
        $mail->setText($request->get('TextBody'));
        $mail->subject($request->get('Subject'));
        $mail->setReplyEmail($request->get('ToFull')[0]['Email']);

        foreach ($this->getAttachments($request) as $attachment) {
            $mail->attachData($attachment['data'], $attachment['name']);
        }

        $this->app->mail->send($mail);

        return response('SUCCESS');
    }

    /**
     * Get a list of attachments connected to this e-mail
     *
     * @param Request $request
     * @return array
     */
    protected function getAttachments(Request $request)
    {
        return array_map(function ($attachment) {
            return [
                'data' => base64_decode($attachment['Content']),
                'name' => $attachment['Name'],
            ];
        }, $request->get('Attachments'));
    }
}