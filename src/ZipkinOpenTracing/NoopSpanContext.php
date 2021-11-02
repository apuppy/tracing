<?php

namespace Yupao\Tracing\ZipkinOpenTracing;

use ArrayIterator;
use Yupao\Tracing\OpenTracing\SpanContext as OTSpanContext;
use Yupao\Tracing\Zipkin\Propagation\TraceContext;

/**
 * @deprecated
 *
 * This shouldn't have been introduced as propagation expects an actual
 * context not a noop one.
 */
class NoopSpanContext implements OTSpanContext, WrappedTraceContext
{
    /**
     * @var TraceContext
     */
    private $context;

    private function __construct(TraceContext $context)
    {
        $this->context = $context;
    }

    public static function create(TraceContext $context)
    {
        return new self($context);
    }

    /**
     * @inheritdoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator([]);
    }

    /**
     * @inheritdoc
     */
    public function getBaggageItem(string $key): ?string
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function withBaggageItem(string $key, string $value): OTSpanContext
    {
        return $this;
    }
}
