<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Instrumentation\Http\Server;

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

    /**
     * getRoute returns the route path, sometimes known by the response time
     * e.g. /user/{user_id}
     */
    public function getRoute(): ?string
    {
        return null;
    }
}
