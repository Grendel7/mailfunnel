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
        'email' => env('MAIL_RECIPIENT_EMAIL', 'you@example.com'),
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
    | Reply Domain
    |--------------------------------------------------------------------------
    |
    | The domain name used to generate addresses for outbound messages.
    |
    */
    'reply_domain' => env('MAIL_REPLY_DOMAIN', 'reply.example.com'),
];