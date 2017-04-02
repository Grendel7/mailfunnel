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

        $this->assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertEquals('email.html', $mail->view);
            $this->assertNull($mail->textView);
            $this->assertArrayHasKey('html', $mail->viewData);

            return true;
        });
    }

    public function testSetHtmlBlank()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setHtml('');

        Mail::send($forwardable);

        $this->assertSent(Forwardable::class, function(Forwardable $mail) {
            $mail->build();

            $this->assertNull($mail->view);
//            $this->assertNull($mail->textView);

            return true;
        });
    }

    public function testSetText()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setText('Test Message');

        Mail::send($forwardable);

        $this->assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertNull($mail->view);
            $this->assertEquals('email.text', $mail->textView);
            $this->assertArrayHasKey('text', $mail->viewData);

            return true;
        });
    }

    public function testSetTextBlank()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setText('');

        Mail::send($forwardable);

        $this->assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertNull($mail->view);
//            $this->assertNull($mail->textView);

            return true;
        });
    }

    public function testSetHtmlAndText()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setHtml('<p>Test Message</p>');
        $forwardable->setText('Test Message');

        Mail::send($forwardable);

        $this->assertSent(Forwardable::class, function(Forwardable $mail) {
            $this->assertEquals('email.html', $mail->view);
            $this->assertEquals('email.text', $mail->textView);
            $this->assertArrayHasKey('html', $mail->viewData);
            $this->assertArrayHasKey('text', $mail->viewData);

            return true;
        });
    }

    public function testAddHeader()
    {
        $this->markTestSkipped("Figure out a way to access SwiftMessage from the test");
    }

    public abstract function getForwardable();
}