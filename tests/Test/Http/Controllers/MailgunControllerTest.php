<?php

namespace Test\Http\Controllers;

use App\Mail\InboundMail;
use App\Mail\OutboundMail;
use App\Models\Address;
use App\Models\Message;
use App\ReplyEmail;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\In;
use Laravel\Lumen\Testing\DatabaseMigrations;

class MailgunControllerTest extends \TestCase
{
    use DatabaseMigrations;

    protected $inboundData;
    protected $outboundData;

    public function setUp()
    {
        parent::setUp();

        Mail::fake();

        $inReplyTo = '<'.Uuid::uuid().'@example.com>';

        $this->inboundData = $this->outboundData = [
            'signature' => 'e2bf81924268c1bb24829d297979613dd36cfe2087006e13566114fd3955b998',
            'References' => $inReplyTo,
            'token' => '76f5fd41657e1942a1fe140c6c0cf04e02729ac063b44abe1d',
            'Message-Id' => '<'.Uuid::uuid().'@example.com>',
            'X-Mailgun-Variables' => '{"my_var_1": "Mailgun Variable #1", "my-var-2": "awesome"}',
            'stripped-text' => 'Hi Alice, This is Bob. I also attached a file.',
            'sender' => 'bob@example.com',
            'attachment-count' => 0,
            'In-Reply-To' => $inReplyTo,
            'content-id-map' => '"<part1.04060802.06030207@example.com>": "attachment-1"}',
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:17.0) Gecko/20130308 Thunderbird/17.0.4',
            'body-plain' => 'Hi Alice, This is Bob. I also attached a file. Thanks, Bob On 04/26/2013 11:29 AM, Alice wrote: > Hi Bob, > > This is Alice. How are you doing? > > Thanks, > Alice',
            'striped-html' => '<html><head> <meta content="text/html; charset=ISO-8859-1" http-equiv="Content-Type"> </head> <body bgcolor="#FFFFFF" text="#000000"> <div class="moz-cite-prefix"> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);">Hi Alice,</div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);"><br> </div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);">This is Bob.<span class="Apple-converted-space">&#160;<img width="33" alt="" height="15" src="cid:part1.04060802.06030207@example.com"></span></div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);"><br> I also attached a file.<br> <br> </div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);">Thanks,</div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);">Bob</div> <br> On 04/26/2013 11:29 AM, Alice wrote:<br> </div> <br> </body></html>',
            'Sender' => 'bob@example.com',
            'Mime-Version' => '1.0',
            'Content-Type' => 'multipart/mixed; boundary="------------020601070403020003080006"',
            'from' => 'Bob <bob@example.com>',
            'Date' => 'Fri, 26 Apr 2013 11:50:29 -0700',
            'Received' => 'by luna.mailgun.net with SMTP mgrt 8788212249833; Fri, 26 Apr 2013 18:50:30 +0000',
            'To' => 'Alice <alice@example.com>',
            'subject' => 'Re: Sample POST request',
            'timestamp' => '1482412978',
            'body-html' => '<html> <head> <meta content="text/html; charset=ISO-8859-1" http-equiv="Content-Type"> </head> <body text="#000000" bgcolor="#FFFFFF"> <div class="moz-cite-prefix"> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);">Hi Alice,</div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);"><br> </div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);">This is Bob.<span class="Apple-converted-space">&nbsp;<img alt="" src="cid:part1.04060802.06030207@example.com" height="15" width="33"></span></div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);"><br> I also attached a file.<br> <br> </div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);">Thanks,</div> <div style="color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.666666984558105px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: auto; word-spacing: 0px; -webkit-text-size-adjust: auto; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);">Bob</div> <br> On 04/26/2013 11:29 AM, Alice wrote:<br> </div> <blockquote cite="mid:517AC78B.5060404@example.com" type="cite">Hi Bob, <br> <br> This is Alice. How are you doing? <br> <br> Thanks, <br> Alice <br> </blockquote> <br> </body> </html>',
            'stripped-signature' => 'Thanks, Bob',
            'From' => 'Bob <bob@example.com>',
            'message-headers' => json_encode([
                ["Received", "by luna.mailgun.net with SMTP mgrt 8788212249833; Fri, 26 Apr 2013 18:50:30 +0000"],
                ["Received", "from [10.20.76.69] (Unknown [50.56.129.169]) by mxa.mailgun.org with ESMTP id 517acc75.4b341f0-worker2; Fri, 26 Apr 2013 18:50:29 -0000 (UTC)"],
                ["Message-Id", "<517ACC75.5010709@example.com>"],
                ["Date", "Fri, 26 Apr 2013 11:50:29 -0700"],
                ["From", "Bob <bob@example.com>"],
                ["User-Agent", "Mozilla/5.0 (X11; Linux x86_64; rv:17.0) Gecko/20130308 Thunderbird/17.0.4"],
                ["Mime-Version", "1.0"],
                ["To", "Alice <alice@example.com>"],
                ["Subject", "Re: Sample POST request"],
                ["References", $inReplyTo],
                ["In-Reply-To", $inReplyTo],
                ["X-Mailgun-Variables", "{\"my_var_1\": \"Mailgun Variable #1\", \"my-var-2\": \"awesome\"}"],
                ["Content-Type", "multipart/mixed; boundary=\"------------020601070403020003080006\""],
                ["Sender", "bob@example.com"],
                ["X-Mailgun-Sscore", "0.5"],
            ]),
            'recipient' => 'alice@example.com',
            'Subject' => 'Re: Sample POST request',
        ];

        $this->outboundData['signature'] = '1f96b8e42244d5f6d79d643cad6a755bf99c3fd0ffd06244a27c5a1823982c36';
        $this->outboundData['To'] = $this->outboundData['recipient'] = ReplyEmail::generate('recipient@example.com', 'sender@example.com');
        $this->outboundData['From'] = $this->outboundData['sender'] = config('mailfunnel.recipient.email');
    }

    public function testInbound()
    {
        $this->post('/mailgun/inbound', $this->inboundData);
        $this->assertResponseOk();

        Mail::assertSent(InboundMail::class, function(InboundMail $mail) {
            $this->assertEquals(
                [['address' => config('mail.from.address'), 'name' => 'Bob \'bob@example.com\' via '.$mail->getOriginalToEmail()]],
                $mail->getFrom()
            );
            $this->assertNotNull($mail->getView());
            $this->assertNotNull($mail->getTextView());
            $this->assertNotNull($mail->getTextView());

            return true;
        });

        $message = Message::all()->last();
        $this->assertEquals($this->inboundData['subject'], $message->subject);
        $this->assertEquals($this->inboundData['recipient'], $message->address->email);
        $this->assertEquals($this->inboundData['From'], $message->from);
        $this->assertEquals(Message::STATUS_SENT, $message->status);
        $this->assertEquals('0.5', $message->spam_score);
    }

    public function testInboundIsSpam()
    {
        $this->inboundData['message-headers'] = json_encode(array_map(function($header) {
            if ($header[0] == 'X-Mailgun-Sscore') {
                $header[1] = 10;
            }

            return $header;
        }, json_decode($this->inboundData['message-headers'], true)));

        $this->post('/mailgun/inbound', $this->inboundData);
        $this->assertResponseStatus(406);

        Mail::assertNotSent(InboundMail::class);

        $message = Message::all()->last();
        $this->assertEquals(Message::STATUS_REJECTED_LOCAL, $message->status);
        $this->assertEquals(Message::REASON_SPAM_SCORE, $message->reason);
        $this->assertEquals('10', $message->spam_score);
    }

    public function testInboundIsBlocked()
    {
        $address = new Address(['email' => $this->inboundData['recipient']]);
        $address->is_blocked = true;
        $address->saveOrFail();

        $this->post('/mailgun/inbound', $this->inboundData);
        $this->assertResponseStatus(406);

        Mail::assertNotSent(InboundMail::class);

        $message = Message::all()->last();
        $this->assertEquals(Message::STATUS_REJECTED_LOCAL, $message->status);
        $this->assertEquals(Message::REASON_ADDRESS_BLOCKED, $message->reason);
        $this->assertEquals($address->id, $message->address_id);
    }

    public function testInboundNoFromName()
    {
        $this->inboundData['From'] = $this->inboundData['sender'];

        $this->post('/mailgun/inbound', $this->inboundData);
        $this->assertResponseOk();

        Mail::assertSent(InboundMail::class, function(InboundMail $mail) {
            $this->assertEquals(
                [['address' => config('mail.from.address'), 'name' => 'bob@example.com via '.$mail->getOriginalToEmail()]],
                $mail->getFrom()
            );
            $this->assertNotNull($mail->getView());
            $this->assertNotNull($mail->getTextView());
            $this->assertNotNull($mail->getTextView());

            return true;
        });

        $message = Message::all()->last();
        $this->assertEquals($this->inboundData['sender'], $message->from);
        $this->assertEquals(Message::STATUS_SENT, $message->status);
    }

    public function testInboundNoToName()
    {
        $this->inboundData['To'] = $this->inboundData['recipient'];

        $this->post('/mailgun/inbound', $this->inboundData);
        $this->assertResponseOk();

        Mail::assertSent(InboundMail::class);

        $message = Message::all()->last();
        $this->assertEquals($this->inboundData['recipient'], $message->address->email);
        $this->assertEquals(Message::STATUS_SENT, $message->status);
    }

    public function testInboundBadAuth()
    {
        $this->inboundData['signature'] = str_random();
        $this->post('/mailgun/inbound', $this->inboundData);
        $this->assertResponseStatus(403);

        Mail::assertNotSent(InboundMail::class);
    }

    public function testInboundNoAuth()
    {
        $this->inboundData['signature'] = '';
        $this->post('/mailgun/inbound', $this->inboundData);
        $this->assertResponseStatus(403);

        Mail::assertNotSent(InboundMail::class);
    }

    public function testOutbound()
    {
        $this->post('/mailgun/outbound', $this->outboundData);
        $this->assertResponseOk();

        Mail::assertSent(OutboundMail::class, function(OutboundMail $mail) {
            $this->assertEquals([['address' => 'recipient@example.com', 'name' => null]], $mail->getFrom());
            $this->assertEquals([['address' => 'sender@example.com', 'name' => null]], $mail->getTo());

            return true;
        });
    }

    public function testOutboundNotAuthorized()
    {
        $this->outboundData['From'] = 'hacker@example.com';
        $this->outboundData['sender'] = 'hacker@example.com';

        $this->post('/mailgun/outbound', $this->outboundData);
        $this->assertResponseStatus(406);

        Mail::assertNotSent(OutboundMail::class);
    }

    public function testOutboundBadAuth()
    {
        $this->outboundData['signature'] = str_random();

        $this->post('/mailgun/outbound', $this->outboundData);
        $this->assertResponseStatus(403);

        Mail::assertNotSent(InboundMail::class);
    }

    public function testOutboundNoAuth()
    {
        $this->outboundData['signature'] = '';
        $this->post('/mailgun/outbound', $this->outboundData);
        $this->assertResponseStatus(403);

        Mail::assertNotSent(InboundMail::class);
    }
}
