<?php

namespace Yupao\Tracing\ZipkinOpenTracing;

use ArrayIterator;
use Yupao\Tracing\OpenTracing\SpanContext as OTSpanContext;
use Yupao\Tracing\Zipkin\Propagation\TraceContext;

final class SpanContext implements OTSpanContext, WrappedTraceContext
{
    private $traceContext;
    private $baggageItems;

    private function __construct(TraceContext $traceContext, array $baggageItems = [])
    {
        $this->traceContext = $traceContext;
        $this->baggageItems = $baggageItems;
    }

    /**
     * @param TraceContext $traceContext
     * @return SpanContext
     */
    public static function fromTraceContext(TraceContext $traceContext)
    {
        return new self($traceContext);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->baggageItems);
    }

    /**
     * @inheritdoc
     */
    public function getBaggageItem(string $key): ?string
    {
        return \array_key_exists($key, $this->baggageItems) ? $this->baggageItems[$key] : null;
    }

    /**
     * @inheritdoc
     */
    public function withBaggageItem(string $key, string $value): OTSpanContext
    {
        return new self($this->traceContext, [$key => $value] + $this->baggageItems);
    }

    /**
     * @inheritdoc
     */
    public function getContext()
    {
        return $this->traceContext;
    }
}
