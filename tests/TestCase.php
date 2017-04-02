<?php

use Illuminate\Support\Facades\Mail;

class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function assertSent($class, callable $callable)
    {
        Mail::assertSent($class, function(\App\Mail\Forwardable $mail) use ($callable) {
            $mail->build();

            return call_user_func($callable, $mail);
        });
    }
}
