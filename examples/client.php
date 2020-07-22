<?php

require_once __DIR__.'/../vendor/autoload.php';

use Flc\Http\Client;

// $ret = Client::get('https://request.worktile.com/i0z0lKilS', [
//     'user' => 123123,
// ])->body();

// $ret = Client::baseUrl('https://request.worktile.com/i0z0lKilS')
$ret = Client::baseUrl('https://flc.io/')
    ->get('/', ['a' => 1, 'b' => 21123]);

print_r($ret->body());
