<?php

namespace App\Http\Controllers\Mailer;

use App\Http\Controllers\Controller;
use App\Mail\InboundMail;
use App\Mail\OutboundMail;
use App\ReplyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        $mail->setSpamScore(array_first($request->input('Headers', []), function($header) {
            return $header['Name'] == 'X-Spam-Score';
        })['Value']);
        $mail->setHtml(html_entity_decode($request->input('HtmlBody')));
        $mail->setText($request->input('TextBody'));
        $mail->subject($request->input('Subject'));
        $mail->setOriginalTo($request->input('OriginalRecipient'));

        if (!empty($request->input('FromName'))) {
            $mail->setOriginalFrom(trim($request->input('FromName'), '"')." <{$request->input('From')}>");
        } else {
            $mail->setOriginalFrom($request->input('From'));
        }

        foreach ($this->getAttachments($request) as $attachment) {
            $mail->attachData($attachment['data'], $attachment['name']);
        }

        foreach($request->input('Headers', []) as $header) {
            $mail->addHeader($header['Name'], $header['Value']);
        }

        if ($mail->validate($request->all())) {
            Mail::send($mail);
        }

        return response('SUCCESS');
    }

    /**
     * Process an outbound e-mail from Postmark
     *
     * @param Request $request
     * @return string
     */
    public function outbound(Request $request)
    {
        if (!ReplyEmail::isAuthorized($request->input('FromFull.Email'))) {
            return response('UNAUTHORIZED');
        }

        $mail = new OutboundMail('postmark');
        $mail->setHtml(html_entity_decode($request->input('HtmlBody')));
        $mail->setText($request->input('TextBody'));
        $mail->subject($request->input('Subject'));
        $mail->setReplyEmail($request->input('ToFull')[0]['Email']);

        foreach ($this->getAttachments($request) as $attachment) {
            $mail->attachData($attachment['data'], $attachment['name']);
        }

        Mail::send($mail);

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
        }, $request->input('Attachments'));
    }
}