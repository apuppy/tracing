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
