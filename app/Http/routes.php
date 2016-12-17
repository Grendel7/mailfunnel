<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return view('welcome');
});

$app->get('reply', 'ReplyController@create');
$app->post('reply', 'ReplyController@store');

$app->post('mailgun/inbound', ['middleware' => 'mailgun', 'uses' => 'MailgunController@inbound']);
$app->post('mailgun/outbound', ['middleware' => 'mailgun', 'uses' => 'MailgunController@outbound']);

$app->post('postmark/inbound', ['middleware' => 'basic_auth:postmark', 'uses' => 'PostmarkController@inbound']);
$app->post('postmark/outbound', ['middleware' => 'basic_auth:postmarkh', 'uses' => 'PostmarkController@outbound']);