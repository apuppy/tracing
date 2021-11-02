<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Instrumentation\Http\Client;

use Yupao\Tracing\Zipkin\Instrumentation\Http\Response as HttpResponse;

abstract class Response extends HttpResponse
{
    /**
     * {@inheritdoc}
     */
    public function getRequest(): ?Request
    {
        return null;
    }
}
