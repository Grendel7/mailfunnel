<?php

use App\ReplyEmail;

class ReplyEmailTest extends TestCase
{
    public $replyEmailAddress = 'eyJmcm9tIjoiVGVzdCBTZW5kZXIgPHNlbmRlckBleGFtcGxlLmNvbT4iLCJ0byI6IlRlc3QgUmVjZWl2ZXIgPHJlY2VpdmVyQGV4YW1wbGUuY29tPiJ9@reply.example.com';
    public $fromName = 'Test Sender';
    public $fromEmail = 'sender@example.com';
    public $toName = 'Test Receiver';
    public $toEmail = 'receiver@example.com';

    public $replyEmail;

    public function setUp()
    {
        parent::setUp();

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

    public function testExtractAddress()
    {
        $this->assertEquals($this->replyEmail->getData(), [
            'from' => [
                'name' => $this->fromName,
                'email' => $this->fromEmail,
            ],
            'to' => [
                'name' => $this->toName,
                'email' => $this->toEmail,
            ]
        ]);
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
}
