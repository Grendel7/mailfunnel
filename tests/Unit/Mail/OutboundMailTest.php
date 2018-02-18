<?php

namespace Test\Mail;

use App\Mail\OutboundMail;
use App\ReplyEmail;
use Illuminate\Support\Facades\Mail;

class OutboundMailTest extends \TestCase
{
    use ForwardableTest;

    /**
     * @var OutboundMail
     */
    public $outboundMail;
    public $replyEmail;

    public function setUp()
    {
        parent::setUp();

        $this->outboundMail = new OutboundMail('phpunit');
        $this->replyEmail = ReplyEmail::generate('Test Sender <sender@example.com>', 'Test Receiver <receiver@example.com>');
        $this->outboundMail->setReplyEmail($this->replyEmail);

        Mail::fake();
    }

    public function testContructor()
    {
        new OutboundMail(str_random());
    }

    public function testSetReplyEmail()
    {
        $this->outboundMail->setText('test');

        Mail::send($this->outboundMail);

        $this->assertSent(OutboundMail::class, function (OutboundMail $mail) {
            $this->assertEquals([['address' => 'sender@example.com', 'name' => 'Test Sender']], $mail->from);
            $this->assertEquals([['address' => 'receiver@example.com', 'name' => 'Test Receiver']], $mail->to);

            return true;
        });
    }

    public function getForwardable()
    {
        return $this->outboundMail;
    }
}
