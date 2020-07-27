<?php

namespace Flc\Http;

use InvalidArgumentException;

/**
 * HTTP 客户端工具类
 *
 * @author Flc <2020-7-22 21:07:51>
 */
class Client
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * 连接示例
     *
     * @var array
     */
    protected $requests = [];

    /**
     * 创建新的 客户端链接示例 实例
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 获取一个单例连接
     *
     * @param string $name
     *
     * @return \Flc\Http\Request
     */
    public function request($name = null)
    {
        $name = $name ?? $this->getDefaultRequest();

        if (! isset($this->requests[$name])) {
            $this->requests[$name] = $this->resolve($name);
        }

        return $this->requests[$name];
    }

    /**
     * 返回默认的连接名
     *
     * @return string
     */
    protected function getDefaultRequest()
    {
        return $this->app['config']['http.default'];
    }

    /**
     * 通过别名生成连接实例
     *
     * @param string $name
     *
     * @return \Flc\Http\Request
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        return $this->create($config);
    }

    /**
     * 获取配置
     *
     * @param string $name 配置别名
     *
     * @return array
     */
    protected function getConfig($name)
    {
        $servers = $this->app['config']['http.servers'];

        if (! isset($servers[$name])) {
            throw new InvalidArgumentException("Server [{$name}] not configured.");
        }

        return $servers[$name];
    }

    /**
     * 通过配置创建相关实例
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
