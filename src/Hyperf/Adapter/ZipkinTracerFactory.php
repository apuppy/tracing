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
namespace Yupao\Tracing\Hyperf\Adapter;

use Hyperf\Contract\ConfigInterface;
use Yupao\Tracing\Hyperf\Contract\NamedFactoryInterface;
use Yupao\Tracing\Zipkin\Endpoint;
use Yupao\Tracing\Zipkin\Reporters\Http;
use Yupao\Tracing\Zipkin\Samplers\BinarySampler;
use Yupao\Tracing\Zipkin\TracingBuilder;
use Yupao\Tracing\ZipkinOpenTracing\Tracer;

class ZipkinTracerFactory implements NamedFactoryInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var HttpClientFactory
     */
    private $clientFactory;

    /**
     * @var string
     */
    private $prefix = 'opentracing.zipkin.';

    public function __construct(ConfigInterface $config, HttpClientFactory $clientFactory)
    {
        $this->config = $config;
        $this->clientFactory = $clientFactory;
    }

    public function make(string $name): \Yupao\Tracing\OpenTracing\Tracer
    {
        if (! empty($name)) {
            $this->prefix = "opentracing.tracer.{$name}.";
        }
        [$app, $options, $sampler] = $this->parseConfig();
        $endpoint = Endpoint::create($app['name'], $app['ipv4'], $app['ipv6'], $app['port']);
        $reporter = new Http($options, $this->clientFactory);
        $tracing = TracingBuilder::create()
            ->havingLocalEndpoint($endpoint)
            ->havingSampler($sampler)
            ->havingReporter($reporter)
            ->build();
        return new Tracer($tracing);
    }

    private function parseConfig(): array
    {
        // @TODO Detect the ipv4, ipv6, port from server object or system info automatically.
        return [
            $this->getConfig('app', [
                'name' => 'skeleton',
                'ipv4' => '127.0.0.1',
                'ipv6' => null,
                'port' => 9501,
            ]),
            $this->getConfig('options', [
                'timeout' => 1,
            ]),
            $this->getConfig('sampler', BinarySampler::createAsAlwaysSample()),
        ];
    }

    private function getConfig(string $key, $default)
    {
        return $this->config->get($this->prefix . $key, $default);
    }
}
