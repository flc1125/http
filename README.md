# PHP HTTP 客户端

> 该扩展基于 Laravel 的 [illuminate/http ](https://github.com/illuminate/http) 扩展进行简化独立而得，核心源码更多为官方源码。

## 安装

## 使用

> 拷贝官方的，:sweat_smile: :sweat_smile:

### 创建请求

你可以通过 `get`、 `post`、 `put`、 `patch` 和 `delete` 方法来创建请求。首先，让我们先看一下如何发出一个基础的 `GET` 请求：

```php
<?php

use Flc\Http\Http;

$response = Http::get('http://test.com');
```

`get` 方法返回一个 `Flc\Http\Response` 的实例，该实例提供了大量的方法来检查请求的响应：

```php
<?php

$response->body() : string;
$response->json() : array;
$response->status() : int;
$response->ok() : bool;
$response->successful() : bool;
$response->serverError() : bool;
$response->clientError() : bool;
$response->header($header) : string;
$response->headers() : array;
```

`Flc\Http\Response` 对象同样实现了 PHP 的 `ArrayAccess` 接口，这代表着你可以直接访问响应的 JSON 数据。

```php
<?php

return Http::get('http://test.com/users/1')['name'];
```

### 请求数据

大多数情况下，`POST`、 `PUT` 和 `PATCH` 携带着额外的请求数据是相当常见的。所以，这些方法的第二个参数接受一个包含着请求数据的数组。默认情况下，这些数据会使用 `application/json` 类型随请求发送。

```php
<?php

$response = Http::post('http://test.com/users', [
    'name' => 'Steve',
    'role' => 'Network Administrator',
]);
```

### 发送 URL 编码的请求

如果你希望使用 `application/x-www-form-urlencoded` 作为请求的数据类型，你可以在创建请求前调用 `asForm` 方法：

```php
<?php

$response = Http::asForm()->post('http://test.com/users', [
    'name' => 'Sara',
    'role' => 'Privacy Consultant',
]);
```

### 发送 Multipart 请求

如果你希望将文件作为 Multipart 请求发送，你应该在创建请求前调用 `attach` 方法。该方法接受文件的标识符（相当于 HTML Input 的 `name` 属性）以及其内容。你也可以在第三个参数传入自定义的文件名称，这不是必须的。

```php
<?php

$response = Http::attach(
    'attachment', file_get_contents('photo.jpg'), 'photo.jpg'
)->post('http://test.com/attachments');
```

除了传递文件的原始内容，你也可以传递 `Stream` 流数据：

```php
<?php

$photo = fopen('photo.jpg', 'r');

$response = Http::attach(
    'attachment', $photo, 'photo.jpg'
)->post('http://test.com/attachments');
```

### 请求头

你可以通过 `withHeaders` 方法添加请求头。该方法接受一个数组格式的键值对。

```php
<?php

$response = Http::withHeaders([
    'X-First' => 'foo',
    'X-Second' => 'bar'
])->post('http://test.com/users', [
    'name' => 'Taylor',
]);
```

### 认证

你可以使用 `withBasicAuth` 和 `withDigestAuth` 方法来分别指定使用 `basic` 或是 `digest` 认证方式：

```php
<?php

// Basic 认证...
$response = Http::withBasicAuth('taylor@laravel.com', 'secret')->post(...);

// Digest 认证...
$response = Http::withDigestAuth('taylor@laravel.com', 'secret')->post(...);
```

### Bearer Token（Token 令牌）

如果你想要为你的请求快速添加 `Authorization` Token 令牌请求头，你可以使用 `withToken` 方法：

```php
<?php

$response = Http::withToken('token')->post(...);
```

### 重试

如果你希望你的 HTTP 客户端在发生错误时自动重新发送请求，你可以使用 `retry` 方法。该方法接受两个参数：重新尝试次数以及重试等待时间（毫秒）：

```php
<?php

$response = Http::retry(3, 100)->post(...);
```

如果所有的请求都失败了，`Flc\Http\RequestException` 异常将会被抛出。

### 错误处理

跟 Guzzle 的默认行为不同，Laravel HTTP 客户端并不会在客户端或服务端错误时抛出异常（`400` 及 `500` 状态码）。你可以通过 `successful`、 `clientError` 或是 `serverError` 方法来判断是否发生错误：

```php
<?php

// 确认状态码是否在 200 到 300 之间（包含 200）
$response->successful();

// 确认是否发生了 400 级别的错误（以 4 开头的状态码）
$response->clientError();

// 确认是否发生了 500 级别的错误（以 5 开头的状态码）
$response->serverError();
```

### 抛出异常

如果你希望请求在发生客户端或服务端错误时抛出 `Flc\Http\RequestException` 异常，你可以在请求实例上调用 `throw` 方法：

```php
<?php

$response = Http::post(...);

// 在客户端或服务端错误发生时抛出异常
$response->throw();

return $response['user']['id'];
```

`Flc\Http\RequestException` 实例拥有一个 `$response` 公共属性，该属性允许你检查返回的响应。

如果没有发生错误，`throw` 方法将返回响应实例，你可以在其上进行其他操作：

```php
<?php

return Http::post(...)->throw()->json();
```

## LICENSE

MIT
