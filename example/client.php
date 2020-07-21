<?php 

require_once __DIR__.'/../vendor/autoload.php';

use Flc\Http\Http;

// $ret = Http::get('https://request.worktile.com/i0z0lKilS', [
//     'user' => 123123,
// ])->body();

// $ret = Http::baseUrl('http://localhost:8001')->get('password/encrypt', [
//     'user' => 123123,
// ]);

$ret = Http::baseUrl('https://request.worktile.com/i0z0lKilS')->withBody('asdfasdf', 'text/plain')->get('/');

print_r($ret->json());
