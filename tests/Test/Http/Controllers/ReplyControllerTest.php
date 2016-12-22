<?php

namespace Test\Http\Controllers;

use App\ReplyEmail;

class ReplyControllerTest extends \TestCase
{

    public function testCreate()
    {
        $this->get('/reply')->assertResponseOk();
    }

    public function testStore()
    {
        $this->post('/reply', [
            'from_name' => 'Test Sender',
            'from_email' => 'sender@example.com',
            'to_name' => 'Test Receiver',
            'to_email' => 'receiver@example.com',
        ]);

        $this->assertTrue(str_contains(
            $this->response->content(),
            ReplyEmail::generate("Test Sender <sender@example.com>", "Test Receiver <receiver@example.com>")
        ));
    }

    public function testStoreInvalid()
    {
        $this->post('/reply', [
            'from_name' => 'Test Sender',
            'from_email' => 'sender',
            'to_name' => 'Test Receiver',
            'to_email' => 'receiver',
        ]);

        $this->assertResponseStatus(422);
    }
}