<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Yupao\Tracing;

use GuzzleHttp\Client;
use Yupao\Tracing\Hyperf\Listener\DbQueryExecutedListener;
use Jaeger\ThriftUdpTransport;
use Yupao\Tracing\Hyperf\SpanTagManager;
use Yupao\Tracing\Hyperf\SpanTagManagerFactory;
use Yupao\Tracing\Hyperf\SwitchManager;
use Yupao\Tracing\Hyperf\SwitchManagerFactory;
use Yupao\Tracing\Hyperf\TracerFactory;
use Yupao\Tracing\OpenTracing\Tracer;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Tracer::class => TracerFactory::class,
                SwitchManager::class => SwitchManagerFactory::class,
                SpanTagManager::class => SpanTagManagerFactory::class,
                Client::class => Client::class,
            ],
            'listeners' => [
                DbQueryExecutedListener::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                    'class_map' => [
                        ThriftUdpTransport::class => __DIR__ . '/../class_map/ThriftUdpTransport.php',
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for tracer.',
                    'source' => __DIR__ . '/../publish/opentracing.php',
                    'destination' => BASE_PATH . '/config/autoload/opentracing.php',
                ],
            ],
        ];
    }
}
