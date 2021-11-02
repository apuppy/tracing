<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Instrumentation\Http\Client\Psr18;

use Yupao\Tracing\Zipkin\Tracer;
use Yupao\Tracing\Zipkin\SpanCustomizerShield;
use Yupao\Tracing\Zipkin\Propagation\TraceContext;
use Yupao\Tracing\Zipkin\Kind;
use Yupao\Tracing\Zipkin\Instrumentation\Http\Client\Psr18\Propagation\RequestHeaders;
use Yupao\Tracing\Zipkin\Instrumentation\Http\Client\HttpClientParser;
use Yupao\Tracing\Zipkin\Instrumentation\Http\Client\HttpClientTracing;
use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Client\ClientInterface;

final class Client implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    private $delegate;

    /**
     * @var callable(TraceContext,mixed):void
     */
    private $injector;

    /**
     * @var Tracer
     */
    private $tracer;

    /**
     * @var HttpClientParser
     */
    private $parser;

    /**
     * @var (callable(Request):?bool)|null
     */
    private $requestSampler;

    public function __construct(ClientInterface $client, HttpClientTracing $tracing)
    {
        $this->delegate = $client;
        $this->injector = $tracing->getTracing()->getPropagation()->getInjector(new RequestHeaders());
        $this->tracer = $tracing->getTracing()->getTracer();
        $this->parser = $tracing->getParser();
        $this->requestSampler = $tracing->getRequestSampler();
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $parsedRequest = new Request($request);
        if ($this->requestSampler === null) {
            $span = $this->tracer->nextSpan();
        } else {
            $span = $this->tracer->nextSpanWithSampler(
                $this->requestSampler,
                [$parsedRequest]
            );
        }

        ($this->injector)($span->getContext(), $request);

        if ($span->isNoop()) {
            try {
                return $this->delegate->sendRequest($request);
            } finally {
                $span->finish();
            }
        }

        $span->setKind(Kind\CLIENT);
        // If span is NOOP it does not make sense to add customizations
        // to it like tags or annotations.
        $spanCustomizer = new SpanCustomizerShield($span);
        $this->parser->request($parsedRequest, $span->getContext(), $spanCustomizer);

        try {
            $span->start();
            $response = $this->delegate->sendRequest($request);
            $this->parser->response(new Response($response, $parsedRequest), $span->getContext(), $spanCustomizer);
            return $response;
        } catch (Throwable $e) {
            $span->setError($e);
            throw $e;
        } finally {
            $span->finish();
        }
    }
}
