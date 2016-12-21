<?php

namespace Test\Mail;

use App\Mail\Forwardable;
use Illuminate\Support\Facades\Mail;

trait ForwardableTest
{

    public function testSetHtml()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setHtml('<p>Test Message</p>');

        Mail::send($forwardable);

        Mail::assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertEquals('email.html', $mail->getView());
            $this->assertNull($mail->getTextView());
            $this->assertArrayHasKey('html', $mail->getViewData());

            return true;
        });
    }

    public function testSetHtmlBlank()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setHtml('');

        Mail::send($forwardable);

        Mail::assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertNull($mail->getView());
//            $this->assertNull($mail->getTextView());

            return true;
        });
    }

    public function testSetText()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setText('Test Message');

        Mail::send($forwardable);

        Mail::assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertNull($mail->getView());
            $this->assertEquals('email.text', $mail->getTextView());
            $this->assertArrayHasKey('text', $mail->getViewData());

            return true;
        });
    }

    public function testSetTextBlank()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setText('');

        Mail::send($forwardable);

        Mail::assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertNull($mail->getView());
//            $this->assertNull($mail->getTextView());

            return true;
        });
    }

    public function testSetHtmlAndText()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setHtml('<p>Test Message</p>');
        $forwardable->setText('Test Message');

        Mail::send($forwardable);

        Mail::assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertEquals('email.html', $mail->getView());
            $this->assertEquals('email.text', $mail->getTextView());
            $this->assertArrayHasKey('html', $mail->getViewData());
            $this->assertArrayHasKey('text', $mail->getViewData());

            return true;
        });
    }

    public function testAddHeader()
    {
        $this->markTestSkipped("Figure out a way to access SwiftMessage from the test");
    }

    public abstract function getForwardable();
}