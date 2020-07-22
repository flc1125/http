<?php

require_once __DIR__.'/../vendor/autoload.php';

use Flc\Http\Http;

// $ret = Http::get('https://request.worktile.com/i0z0lKilS', [
//     'user' => 123123,
// ])->body();

// $ret = Http::baseUrl('http://localhost:8001')->get('password/encrypt', [
//     'user' => 123123,
// ]);

$ret = Http::baseUrl('https://request.worktile.com/i0z0lKilS')
    ->withToken('taylor@laravel.com')
    ->post('/', ['a' => 1, 'b' => 21123]);

print_r($ret->body());
print_r($ret->cookies());
