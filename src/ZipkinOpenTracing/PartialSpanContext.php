<?php

namespace Yupao\Tracing\ZipkinOpenTracing;

use ArrayIterator;
use Yupao\Tracing\OpenTracing\SpanContext as OTSpanContext;
use Yupao\Tracing\Zipkin\Propagation\SamplingFlags;

/**
 * Used to wrap SamplingFlags coming from extractor
 */
final class PartialSpanContext implements OTSpanContext, WrappedTraceContext
{
    /**
     * @var SamplingFlags
     */
    private $samplingFlags;

    /**
     * @var array
     */
    private $baggageItems;

    private function __construct(SamplingFlags $samplingFlags, array $baggageItems = [])
    {
        $this->samplingFlags = $samplingFlags;
        $this->baggageItems = $baggageItems;
    }

    /**
     * @param SamplingFlags $samplingFlags
     * @return PartialSpanContext
     */
    public static function fromSamplingFlags(SamplingFlags $samplingFlags)
    {
        return new self($samplingFlags);
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
        return new self($this->samplingFlags, [$key => $value] + $this->baggageItems);
    }

    /**
     * @inheritdoc
     */
    public function getContext()
    {
        return $this->samplingFlags;
    }
}
