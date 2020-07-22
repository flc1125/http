<?php

namespace Flc\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

/**
 * 创建一个客户端请求实例
 *
 * @author Flc <2020-7-21 10:21:20>
 *
 * @see https://github.com/illuminate/http/blob/master/Client/PendingRequest.php
 */
class Request
{
    /**
     * The base URL for the request.
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * The request body format.
     *
     * @var string
     */
    protected $bodyFormat;

    /**
     * The raw body for the request.
     *
     * @var string
     */
    protected $pendingBody;

    /**
     * The pending files for the request.
     *
     * @var array
     */
    protected $pendingFiles = [];

    /**
     * The request cookies.
     *
     * @var array
     */
    protected $cookies;

    /**
     * The request options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The number of times to try the request.
     *
     * @var int
     */
    protected $tries = 1;

    /**
     * The number of milliseconds to wait between retries.
     *
     * @var int
     */
    protected $retryDelay = 100;

    /**
     * Create a new HTTP Client instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->asJson();

        $this->options = [
            'http_errors' => false,
        ];
    }

    /**
     * Set the base URL for the pending request.
     *
     * @param string $url
     *
     * @return $this
     */
    public function baseUrl(string $url)
    {
        $this->baseUrl = $url;

        return $this;
    }

    /**
     * Attach a raw body to the request.
     *
     * @param resource|string $content
     * @param string          $contentType
     *
     * @return $this
     */
    public function withBody($content, $contentType)
    {
        $this->bodyFormat('body');

        $this->pendingBody = $content;

        $this->contentType($contentType);

        return $this;
    }

    /**
     * Indicate the request contains JSON.
     *
     * @return $this
     */
    public function asJson()
    {
        return $this->bodyFormat('json')->contentType('application/json');
    }

    /**
     * Indicate the request contains form parameters.
     *
     * @return $this
     */
    public function asForm()
    {
        return $this->bodyFormat('form_params')->contentType('application/x-www-form-urlencoded');
    }

    /**
     * Attach a file to the request.
     *
     * @param string      $name
     * @param string      $contents
     * @param string|null $filename
     * @param array       $headers
     *
     * @return $this
     */
    public function attach($name, $contents, $filename = null, array $headers = [])
    {
        $this->asMultipart();

        $this->pendingFiles[] = array_filter([
            'name'     => $name,
            'contents' => $contents,
            'headers'  => $headers,
            'filename' => $filename,
        ]);

        return $this;
    }

    /**
     * Indicate the request is a multi-part form request.
     *
     * @return $this
     */
    public function asMultipart()
    {
        return $this->bodyFormat('multipart');
    }

    /**
     * Specify the body format of the request.
     *
     * @param string $format
     *
     * @return $this
     */
    public function bodyFormat(string $format)
    {
        $this->bodyFormat = $format;

        return $this;
    }

    /**
     * Specify the request's content type.
     *
     * @param string $contentType
     *
     * @return $this
     */
    public function contentType(string $contentType)
    {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    /**
     * Indicate that JSON should be returned by the server.
     *
     * @return $this
     */
    public function acceptJson()
    {
        return $this->accept('application/json');
    }

    /**
     * Indicate the type of content that should be returned by the server.
     *
     * @param string $contentType
     *
     * @return $this
     */
    public function accept($contentType)
    {
        return $this->withHeaders(['Accept' => $contentType]);
    }

    /**
     * Add the given headers to the request.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        $this->options = array_merge_recursive($this->options, [
            'headers' => $headers,
        ]);

        return $this;
    }

    /**
     * Specify the basic authentication username and password for the request.
     *
     * @param string $username
     * @param string $password
     *
     * @return $this
     */
    public function withBasicAuth(string $username, string $password)
    {
        $this->options['auth'] = [$username, $password];

        return $this;
    }

    /**
     * Specify the digest authentication username and password for the request.
     *
     * @param string $username
     * @param string $password
     *
     * @return $this
     */
    public function withDigestAuth($username, $password)
    {
        $this->options['auth'] = [$username, $password, 'digest'];

        return $this;
    }

    /**
     * Specify an authorization token for the request.
     *
     * @param string $token
     * @param string $type
     *
     * @return $this
     */
    public function withToken($token, $type = 'Bearer')
    {
        $this->options['headers']['Authorization'] = trim($type.' '.$token);

        return $this;
    }

    /**
     * Specify the cookies that should be included with the request.
     *
     * @param array  $cookies
     * @param string $domain
     *
     * @return $this
     */
    public function withCookies(array $cookies, string $domain)
    {
        $this->options = array_merge_recursive($this->options, [
                'cookies' => CookieJar::fromArray($cookies, $domain),
            ]);

        return $this;
    }

    /**
     * Indicate that redirects should not be followed.
     *
     * @return $this
     */
    public function withoutRedirecting()
    {
        $this->options['allow_redirects'] = false;

        return $this;
    }

    /**
     * Indicate that TLS certificates should not be verified.
     *
     * @return $this
     */
    public function withoutVerifying()
    {
        $this->options['verify'] = false;

        return $this;
    }

    /**
     * Specify the timeout (in seconds) for the request.
     *
     * @param int $seconds
     *
     * @return $this
     */
    public function timeout(int $seconds)
    {
        $this->options['timeout'] = $seconds;

        return $this;
    }

    /**
     * Specify the number of times the request should be attempted.
     *
     * @param int $times
     * @param int $sleep
     *
     * @return $this
     */
    public function retry(int $times, int $sleep = 0)
    {
        $this->tries = $times;
        $this->retryDelay = $sleep;

        return $this;
    }

    /**
     * Merge new options into the client.
     *
     * @param array $options
     *
     * @return $this
     */
    public function withOptions(array $options)
    {
        $this->options = array_merge_recursive($this->options, $options);

        return $this;
    }

    /**
     * Issue a GET request to the given URL.
     *
     * @param string            $url
     * @param array|string|null $query
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function get($url, $query = null)
    {
        return $this->send('GET', $url, [
            'query' => $query,
        ]);
    }

    /**
     * Issue a HEAD request to the given URL.
     *
     * @param string            $url
     * @param array|string|null $query
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function head($url, $query = null)
    {
        return $this->send('HEAD', $url, [
            'query' => $query,
        ]);
    }

    /**
     * Issue a POST request to the given URL.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function post($url, $data = [])
    {
        return $this->send('POST', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a PATCH request to the given URL.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function patch($url, $data = [])
    {
        return $this->send('PATCH', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a PUT request to the given URL.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function put($url, $data = [])
    {
        return $this->send('PUT', $url, [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Issue a DELETE request to the given URL.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function delete($url, $data = [])
    {
        return $this->send('DELETE', $url, empty($data) ? [] : [
            $this->bodyFormat => $data,
        ]);
    }

    /**
     * Send the request to the given URL.
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Exception
     */
    public function send(string $method, string $url, array $options = [])
    {
        $url = ltrim(rtrim($this->baseUrl, '/').'/'.ltrim($url, '/'), '/');

        if (isset($options[$this->bodyFormat])) {
            if ($this->bodyFormat === 'multipart') {
                $options[$this->bodyFormat] = $this->parseMultipartBodyFormat($options[$this->bodyFormat]);
            } elseif ($this->bodyFormat === 'body') {
                $options[$this->bodyFormat] = $this->pendingBody;
            }

            if (is_array($options[$this->bodyFormat])) {
                $options[$this->bodyFormat] = array_merge(
                    $options[$this->bodyFormat], $this->pendingFiles
                );
            }
        }

        [$this->pendingBody, $this->pendingFiles] = [null, []];

        return $this->retryCallback($this->tries ?? 1, function () use ($method, $url, $options) {
            try {
                $response = new Response($this->buildClient()->request($method, $url, $options));
                $response->cookies = $this->cookies;

                if ($this->tries > 1 && ! $response->successful()) {
                    $response->throw();
                }

                return $response;
            } catch (ConnectException $e) {
                throw new ConnectionException($e->getMessage(), 0, $e);
            }
        }, $this->retryDelay ?? 100);
    }

    /**
     * Parse multi-part form data.
     *
     * @param array $data
     *
     * @return array|array[]
     */
    protected function parseMultipartBodyFormat(array $data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[] = is_array($value) ? $value : ['name' => $key, 'contents' => $value];
        }

        return $result;
    }

    /**
     * Build the Guzzle client.
     *
     * @return \GuzzleHttp\Client
     */
    public function buildClient()
    {
        return new Client([
            'cookies' => true,
        ]);
    }

    /**
     * Retry an operation a given number of times.
     *
     * @param int           $times
     * @param callable      $callback
     * @param int           $sleep
     * @param callable|null $when
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function retryCallback($times, callable $callback, $sleep = 0, $when = null)
    {
        $attempts = 0;

        beginning:
        $attempts++;
        --$times;

        try {
            return $callback($attempts);
        } catch (Exception $e) {
            if ($times < 1 || ($when && ! $when($e))) {
                throw $e;
            }

            if ($sleep) {
                usleep($sleep * 1000);
            }

            goto beginning;
        }
    }
}
