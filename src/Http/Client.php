<?php

namespace Flc\Http;

/**
 * HTTP 客户端工具类
 *
 * @author Flc <2020-7-22 21:07:51>
 */
class Client
{
    /**
     * 自定义配置创建相关实例
     *
     * @var array
     */
    public static function create($config = [])
    {
        $request = new Request();

        if (! empty($config['base_url'])) {
            $request->baseUrl($config['base_url']);
        }

        if (! empty($config['timeout'])) {
            $request->timeout($config['timeout']);
        }

        return $request;
    }

    /**
     * 魔术方法转发处理
     *
     * @param string $method
     * @param mixed  $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return self::create()->{$method}(...$parameters);
    }
}
