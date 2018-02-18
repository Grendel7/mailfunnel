<?php

namespace Test\Mail;

use App\Mail\InboundMail;
use App\Models\Address;
use App\Models\Domain;
use App\Models\Message;
use App\Models\User;
use App\ReplyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use ReflectionClass;
use Tests\TestCase;

class InboundMailTest extends TestCase
{
    use ForwardableTest, RefreshDatabase;

    protected $user;
    protected $domain;

    /**
     * @var InboundMail
     */
    protected $inboundMail;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->domain = Domain::create(['domain' => 'example.com', 'user_id' => $this->user->id]);

        $this->inboundMail = new InboundMail('phpunit');
        $this->inboundMail->setOriginalTo('Test Recipient <recipient@example.com>');
        $this->inboundMail->setOriginalFrom('Test Sender <sender@example.com>');
        $this->inboundMail->subject('Test Subject');

        Mail::fake();
    }

    public function testBuildSetsReplyToAddress()
    {
        $this->inboundMail->build();

        $expected = [
            'address' => ReplyEmail::generate('Test Recipient <recipient@example.com>', 'Test Sender <sender@example.com>'),
            'name' => null,
        ];

        $reflection = new ReflectionClass($this->inboundMail);
        $property = $reflection->getProperty('replyTo');
        $property->setAccessible(true);

        $this->assertEquals([$expected], $property->getValue($this->inboundMail));
    }

    public function testBuildSetsToAddress()
    {
        $this->inboundMail->build();

        $expected = [
            'address' => $this->user->email,
            'name' => $this->user->name,
        ];

        $this->assertEquals([$expected], $this->inboundMail->to);
    }

    public function testBuildSetsFromAddress()
    {
        $this->inboundMail->build();

        $expected = [
            'address' => config('mail.from.address'),
            'name' =>  $this->inboundMail->getSafeOriginalFrom() . ' via ' . $this->inboundMail->getOriginalToEmail(),
        ];

        $this->assertEquals([$expected], $this->inboundMail->from);
    }

    public function testGetSafeOriginalFrom()
    {
        $this->inboundMail->setOriginalFrom('Test Sender <sender@example.com>');
        $this->assertEquals("Test Sender 'sender@example.com'", $this->inboundMail->getSafeOriginalFrom());
    }

    public function testGetSafeOriginalFromWithoutName()
    {
        $this->inboundMail->setOriginalFrom('anonymous@example.com');
        $this->assertEquals('anonymous@example.com', $this->inboundMail->getSafeOriginalFrom());
    }

    public function testGetOriginalToEmail()
    {
        $this->inboundMail->setOriginalTo('Test Receiver <receiver@example.com>');
        $this->assertEquals('receiver@example.com', $this->inboundMail->getOriginalToEmail());
    }

    public function testGetOriginalToEmailWithoutName()
    {
        $this->inboundMail->setOriginalTo('receiver2@example.com');
        $this->assertEquals('receiver2@example.com', $this->inboundMail->getOriginalToEmail());
    }

    public function testValidate()
    {
        $this->assertTrue($this->inboundMail->validate([]));

        $message = Message::all()->last();
        $this->assertFalse($message->is_rejected);
        $this->assertEquals(null, $message->reason);
    }

    public function testValidateAddressAlreadyExists()
    {
        $address = Address::create(['email' => $this->inboundMail->getOriginalToEmail()]);

        $this->assertTrue($this->inboundMail->validate([]));

        $message = Message::all()->last();
        $this->assertEquals($address->id, $message->address_id);
    }

    public function testValidateAddressIsBlocked()
    {
        $address = new Address(['email' => $this->inboundMail->getOriginalToEmail()]);
        $address->is_blocked = true;
        $address->domain_id = Domain::where('domain', 'example.com')->first()->id;
        $address->save();

        $this->assertFalse($this->inboundMail->validate([]));
        $message = Message::all()->last();
        $this->assertEquals($address->id, $message->address_id);
        $this->assertTrue($message->is_rejected);
        $this->assertEquals(Message::REASON_ADDRESS_BLOCKED, $message->reason);
    }

    public function testValidateMessageIsSpam()
    {
        $this->inboundMail->setSpamScore(PHP_INT_MAX);

        $this->assertFalse($this->inboundMail->validate([]));

        $message = Message::all()->last();
        $this->assertTrue($message->is_rejected);
        $this->assertEquals(Message::REASON_SPAM_SCORE, $message->reason);
    }

    public function testValidateLogsRequestContents()
    {
        Log::shouldReceive('info')->once()->with('Received message for provider phpunit', ['foo' => 'bar', 'fux' => 'baz']);

        $this->assertTrue($this->inboundMail->validate(['foo' => 'bar', 'fux' => 'baz']));
    }

    public function getForwardable()
    {
        return $this->inboundMail;
    }
}
