<?php

return [
    'webhook_key' => [

        /*
        |--------------------------------------------------------------------------
        | Inbound Webhook Key
        |--------------------------------------------------------------------------
        |
        | The API key of your inbound domain is used to sign inbound requests.
        |
        */
        'inbound' => env('MAILGUN_WEBHOOK_KEY_INBOUND', 'key-your-inbound-token-here'),

        /*
        |--------------------------------------------------------------------------
        | Outbound Webhook Key
        |--------------------------------------------------------------------------
        |
        | The API key of your outbound domain is used to sign inbound requests.
        |
        */
        'outbound' => env('MAILGUN_WEBHOOK_KEY_OUTBOUND', 'key-your-outbound-token-here'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Spamassassin Threshold
    |--------------------------------------------------------------------------
    |
    | This is the maximum spam score for messages to be forwarded. If messages
    | exceed this spam score, they will not be forwarded. It has been
    | preconfigured with a good starting value, but you may wish to fine tune
    | the value if you get too much spam or legitimate e-mail is being blocked.
    |
    */
    'spamassassin_score' => 6.5,
];