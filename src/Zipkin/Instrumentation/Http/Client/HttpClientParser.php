<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Instrumentation\Http\Client;

use Yupao\Tracing\Zipkin\SpanCustomizer;
use Yupao\Tracing\Zipkin\Propagation\TraceContext;
use Yupao\Tracing\Zipkin\Instrumentation\Http\Response;
use Yupao\Tracing\Zipkin\Instrumentation\Http\Request;

/**
 * Parser includes the methods to obtain meaningful span information
 * out of HTTP request/response elements.
 */
interface HttpClientParser
{
    /**
     * request parses the incoming data related to a request in order to add
     * relevant information to the span under the SpanCustomizer interface.
     *
     * Basic data being tagged is HTTP method, HTTP path but other information
     * such as query parameters can be added to enrich the span information.
     */
    public function request(Request $request, TraceContext $context, SpanCustomizer $span): void;


    /**
     * response parses the response data in order to add relevant information
     * to the span under the SpanCustomizer interface.
     *
     * Basic data being tagged is HTTP status code but other information such
     * as any response header or more granular information based on the response
     * payload can be added.
     */
    public function response(Response $response, TraceContext $context, SpanCustomizer $span): void;
}
