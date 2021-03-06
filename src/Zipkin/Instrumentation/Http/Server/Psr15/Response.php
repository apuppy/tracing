<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Instrumentation\Http\Server\Psr15;

use Yupao\Tracing\Zipkin\Instrumentation\Http\Server\Response as ServerResponse;
use Yupao\Tracing\Zipkin\Instrumentation\Http\Server\Request as ServerRequest;
use Psr\Http\Message\ResponseInterface;

final class Response extends ServerResponse
{
    /**
     * @var ResponseInterface
     */
    private $delegate;

    /**
     * @var Request|null
     */
    private $request;

    public function __construct(
        ResponseInterface $delegate,
        ?Request $request = null
    ) {
        $this->delegate = $delegate;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(): ?ServerRequest
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode(): int
    {
        return $this->delegate->getStatusCode();
    }

    /**
     * {@inheritdoc}
     */
    public function unwrap(): ResponseInterface
    {
        return $this->delegate;
    }
}
