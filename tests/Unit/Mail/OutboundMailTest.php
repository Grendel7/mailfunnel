<?php

namespace Test\Mail;

use App\Mail\OutboundMail;
use App\ReplyEmail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OutboundMailTest extends TestCase
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

    public function testSetReplyEmail()
    {
        $this->outboundMail->setText('test');

        $this->outboundMail->build();

        $this->assertEquals([['address' => 'sender@example.com', 'name' => 'Test Sender']], $this->outboundMail->from);
        $this->assertEquals([['address' => 'receiver@example.com', 'name' => 'Test Receiver']], $this->outboundMail->to);
    }

    public function getForwardable()
    {
        return $this->outboundMail;
    }
}
