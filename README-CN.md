[English](./README.md) | 中文

# 链路追踪

鱼泡网基于openzipkin定制的链路追踪组件

# 快速开始

## 安装composer依赖

```shell
composer require yupao/tracing
```

## (可选) 删除现有的opentracing配置文件

```shell
rm -f {$project_dir}/config/autoload/opentracing.php
```

## 发布配置文件

```shell
php bin/hyperf.php vendor:publish yupao/tracing
```

## 更新aspect配置

```php
# config/autoload/aspects.php
return [
    Yupao\Tracing\Hyperf\Aspect\JsonRpcAspect::class,
];
```

## 更新middlewares配置

```php
# config/autoload/middlewares.php
return [
    'http' => [
        Yupao\Tracing\Hyperf\Middleware\TraceMiddleware::class
    ],
];
```

# 自定义

## 更改自定义追踪的HTTP header头名称

如果您使用了自定义的HTTP头用于追踪。在项目配置.env文件中，将`CUSTOMIZED_TRACE_ID_NAME`设置为相应的HTTP头名称。

```
# 编辑.env
CUSTOMIZED_TRACE_ID_NAME=x-request-id
```