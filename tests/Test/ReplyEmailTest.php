<?php

namespace Test;

use App\ReplyEmail;

class ReplyEmailTest extends \TestCase
{
    public $replyEmailAddress;
    public $fromName = 'Test Sender';
    public $fromEmail = 'sender@example.com';
    public $toName = 'Test Receiver';
    public $toEmail = 'receiver@example.com';

    /**
     * @var ReplyEmail
     */
    public $replyEmail;

    public function setUp()
    {
        parent::setUp();

        $this->replyEmailAddress = ReplyEmail::generate("{$this->fromName} <{$this->fromEmail}>", "{$this->toName} <{$this->toEmail}>");
        $this->replyEmail = new ReplyEmail($this->replyEmailAddress);
    }

    public function testGenerate()
    {
        $from = "{$this->fromName} <{$this->fromEmail}>";
        $to = "{$this->toName} <{$this->toEmail}>";

        $this->assertEquals($this->replyEmailAddress, ReplyEmail::generate($from, $to));
    }

    public function testIsAuthorized()
    {
        $this->assertTrue(ReplyEmail::isAuthorized('you@example.com'));
    }

    public function testIsNotAuthorized()
    {
        $this->assertFalse(ReplyEmail::isAuthorized('hacker@example.com'));
    }

    public function testGetFromEmail()
    {
        $this->assertEquals($this->replyEmail->getFromEmail(), $this->fromEmail);
    }

    public function testGetFromName()
    {
        $this->assertEquals($this->replyEmail->getFromName(), $this->fromName);
    }

    public function testGetToEmail()
    {
        $this->assertEquals($this->replyEmail->getToEmail(), $this->toEmail);
    }

    public function testGetToName()
    {
        $this->assertEquals($this->replyEmail->getToName(), $this->toName);
    }

    public function testExtractAddress()
    {
        $this->assertEquals(['name' => null, 'email' => 'test@example.com'], $this->replyEmail->extractAddress('test@example.com'));
    }
}
