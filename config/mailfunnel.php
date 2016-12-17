<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Recipient
    |--------------------------------------------------------------------------
    |
    | This is the address any e-mail will be forwarded to.
    |
    */

    'recipient' => [
        'name' => env('MAIL_RECIPIENT_NAME', 'You'),
        'email' => env('MAIL_RECIPIENT_EMAIL', 'test@example.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorized Senders
    |--------------------------------------------------------------------------
    |
    | This is a list of e-mail address which can send outbound e-mail for your domain.
    | The recipient defined above can always send outbound mail.
    |
    */

    'authorized_senders' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Duration
    |--------------------------------------------------------------------------
    |
    | Mail Funnel can log the contents of inbound webhooks, which can be useful for debugging purposes.
    | Not recommended with high e-mail volumes, limited database space or potentially unsafe hosting environments.
    |
    */

    'log_days' => 90,
];