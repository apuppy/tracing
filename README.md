English | [中文](./README-CN.md)

# tracing

Customized tracing component based on openzipkin by Yupao

# quickstart

## install dependency

```shell
composer require yupao/tracing
```

## (optional) delete existing opentracing config file

```shell
rm -f {$project_dir}/config/autoload/opentracing.php
```

## publish config file

```shell
php bin/hyperf.php vendor:publish yupao/tracing
```

## change aspect configuration

```php
# config/autoload/aspects.php
return [
    Yupao\Tracing\Hyperf\Aspect\JsonRpcAspect::class,
];
```

## middlewares configuration

```php
# config/autoload/middlewares.php
return [
    'http' => [
        Yupao\Tracing\Hyperf\Middleware\TraceMiddleware::class
    ],
];
```

# customization

## change customized trace header name

If you are using a customized HTTP header for tracing. In the project configuration .env file,
set `CUSTOMIZED_TRACE_ID_NAME` to that name.

```
# edit .env
CUSTOMIZED_TRACE_ID_NAME=x-request-id
```