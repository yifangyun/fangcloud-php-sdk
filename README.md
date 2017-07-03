# 亿方云PHP SDK

## 安装

### 通过composer安装(推荐)

```bash
# 安装composer
curl -sS https://getcomposer.org/installer | php
```

你可以通过composer.phar安装亿方云php sdk

```bash
php composer.phar require fangcloud/fangcloud-sdk-php:@stable
```

你也可以在已有的composer.json文件中加入亿方云php sdk的依赖

```json
{
   "require": {
      "fangcloud/fangcloud-sdk-php": "@stable"
   }
}
```

安装完成后，require composer的autoload文件以使用亿方云php sdk

```php
require 'vendor/autoload.php';
```

### 通过phar安装

前往release页面下载对应版本fangcloud-php-sdk.phar文件

使用时直接require该文件

```php
require 'fangcloud-php-sdk.phar';
```

### 通过autoload.php安装

前往release页面下载对应版本fangcloud-php-sdk.zip压缩文件

解压后require fangcloud-autoload.php

```php
require 'fangcloud-autoload.php';
```



## 创建应用

目前企业管理员可在企业控制台中申请开放平台应用，经平台审核通过以后会得到一组client_id和client_secret。

在使用本sdk使，用户需要使用这些信息初始化应用

```php
YfyAppInfo::init($clientId, $clientSecret, $callbackUri);
```

其中`$callbackUri`是用户申请应用时必须填写的回调地址。

## 快速上手

### API调用

```php
// 初始化应用信息
YfyAppInfo::init($clientId, $clientSecret, $callbackUri);
// 可配置参数
$options = [
  YfyClientOptions::ACCESS_TOKEN => $accessToken,
  YfyClientOptions::REFRESH_TOKEN => $refreshToken
];
// 构造client
$client = new YfyClient($options);
// 调用api
$client->users()->getSelf();
```

其中$options为用户可配置的参数，可以参考Fangcloud\YfyClientOptions中的注释

### 进行授权

本sdk也包括了授权部分的操作（即如何获取access token）

#### 使用授权码模式

本sdk提供了两个方法帮助用户简化授权码流程的交互

```php
# 获取授权url, 让用户跳转到这个url上以完成授权
$authorizationUrl =  $client->oauth()->getAuthorizationUrl();
```

```php
# 在回调页面调用这个函数, sdk会帮助你校验state, 并且构造请求获取access token
$res = $client->oauth()->finishAuthorizationCodeFlow($code, $state);
$accessToken = $res['access_token'];
$refreshToken = $res['refresh_token'];
```

本sdk提供了一个web-demo帮助用户更好理解授权码模式的流程

下载本sdk源码

```
git clone git@github.com:yifangyun/fangcloud-php-sdk.git
```

进入example/web-demo目录，修改YfyClientFactory.php文件，填入你注册的应用信息。

起一个php built-in server

```
php -S localhost:8000
```

打开[http://localhost:8000](http://localhost:8000)以使用该demo，根据提示操作即可



需要注意，在授权码模式中，由于需要校验state参数，需要对用户生成的state参数进行存储，该操作由Fangcloud\PersistentData\PersistentDataHandler调用，PersistentDataHandler的默认实现会将state参数存入php的`$_SESSION`中，因此需要用户开启session，假如用户有自己的需求，则需要实现自己的PersistenDataHandler（推荐做法）。

#### 使用密码模式

```php
$res = $client->oauth()->getTokenByPasswordFlow($username, $password);
$accessToken = $res['access_token'];
$refreshToken = $res['refresh_token'];
```



### 可定制化组件

#### PersistentDataHandler

主要用于实现state参数的存储以及读取，默认实现会将其存入php的`$_SESSION`中，因此需要用户开启session，假如用户有自己的需求，则需要实现自己的PersistenDataHandler（推荐做法）。

#### RandomStringGenerator

用于生成state参数，提供了random_bytes、mcrypt、openssl、urandom四种实现，若用户没有显示指定实现，默认会检测支持哪一种实现，优先级random_bytes>mcrypt>openssl>urandom，用户可以通过Fangcloud\RandomString\RandomStringGeneratorFactory创建指定实现的实例，并且通过option的方式传入YfyClient的构造函数。

```php
$options = [
  YfyClientOptions::RANDOM_STRING_GENERATOR => RandomStringGeneratorFactory::create('random_bytes')
];
$client = new YfyClient(options);
```

当然，用户也可以选择自己实现RandomStringGenerator。

## 其他文档

* [phpdoc](https://yifangyun.github.io/fangcloud-sdk-php)
* [API文档](https://open.fangcloud.com/wiki/v2)