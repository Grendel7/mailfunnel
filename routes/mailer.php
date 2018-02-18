<?php

Route::post('mailgun/inbound', 'MailgunController@inbound')->middleware('mailgun:inbound');
Route::post('mailgun/outbound', 'MailgunController@outbound')->middleware('mailgun:outbound');

Route::post('postmark/inbound', 'PostmarkController@inbound')->middleware('basic_auth:postmark');
Route::post('postmark/outbound', 'PostmarkController@outbound')->middleware('basic_auth:postmark');

Route::post('sendgrid/inbound', 'SendgridController@inbound')->middleware('basic_auth:sendgrid');
Route::post('sendgrid/outbound', 'SendgridController@outbound')->middleware('basic_auth:sendgrid');
