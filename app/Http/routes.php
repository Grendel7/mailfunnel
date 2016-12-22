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

$app->post('mailgun/inbound', ['middleware' => 'mailgun:inbound', 'uses' => 'MailgunController@inbound']);
$app->post('mailgun/outbound', ['middleware' => 'mailgun:outbound', 'uses' => 'MailgunController@outbound']);

$app->post('postmark/inbound', ['middleware' => 'basic_auth:postmark', 'uses' => 'PostmarkController@inbound']);
$app->post('postmark/outbound', ['middleware' => 'basic_auth:postmark', 'uses' => 'PostmarkController@outbound']);

$app->post('sendgrid/inbound', ['middleware' => 'basic_auth:sendgrid', 'uses' => 'SendgridController@inbound']);
$app->post('sendgrid/outbound', ['middleware' => 'basic_auth:sendgrid', 'uses' => 'SendgridController@outbound']);