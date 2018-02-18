<?php

namespace Test\Mail;

trait ForwardableTest
{
    public function testSetHtml()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setHtml('<p>Test Message</p>');

        $forwardable->build();

        $this->assertEquals('email.html', $forwardable->view);
        $this->assertNull($forwardable->textView);
        $this->assertArrayHasKey('html', $forwardable->viewData);
    }

    public function testSetHtmlBlank()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setHtml('');

        $forwardable->build();
        $this->assertNull($forwardable->view);
    }

    public function testSetText()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setText('Test Message');

        $forwardable->build();

        $this->assertNull($forwardable->view);
        $this->assertEquals('email.text', $forwardable->textView);
        $this->assertArrayHasKey('text', $forwardable->viewData);
    }

    public function testSetTextBlank()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setText('');

        $forwardable->build();
        $this->assertNull($forwardable->view);
    }

    public function testSetHtmlAndText()
    {
        $forwardable = $this->getForwardable();
        $forwardable->setHtml('<p>Test Message</p>');
        $forwardable->setText('Test Message');

        $forwardable->build();

        $this->assertEquals('email.html', $forwardable->view);
        $this->assertEquals('email.text', $forwardable->textView);
        $this->assertArrayHasKey('html', $forwardable->viewData);
        $this->assertArrayHasKey('text', $forwardable->viewData);
    }

    public function testAddHeader()
    {
        $this->markTestSkipped("Figure out a way to access SwiftMessage from the test");
    }

    public abstract function getForwardable();
}