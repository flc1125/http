<?php

namespace Flc\Http;

use GuzzleHttp\Client;

/**
 * 123
 */
class Http
{
    public static function __callStatic($method, $parameters)
    {
        return (new Request)->{$method}(...$parameters);
    }
}
