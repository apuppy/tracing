<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Instrumentation\Http\Client\Psr18\Propagation;

use Yupao\Tracing\Zipkin\Propagation\RequestHeaders as BaseRequestHeaders;
use Yupao\Tracing\Zipkin\Propagation\RemoteSetter;
use Yupao\Tracing\Zipkin\Kind;

final class RequestHeaders extends BaseRequestHeaders implements RemoteSetter
{
    public function getKind(): string
    {
        return Kind\CLIENT;
    }
}
