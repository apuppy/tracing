<?php

namespace Yupao\Tracing\ZipkinOpenTracing;

use Yupao\Tracing\OpenTracing\ScopeManager as OTScopeManager;
use Yupao\Tracing\Zipkin\Tracing as ZipkinTracing;
use Yupao\Tracing\Zipkin\Tracer as ZipkinTracer;
use Yupao\Tracing\Zipkin\Timestamp;
use Yupao\Tracing\Zipkin\Propagation\TraceContext;
use Yupao\Tracing\Zipkin\Propagation\SamplingFlags;
use Yupao\Tracing\Zipkin\Propagation\RequestHeaders;
use Yupao\Tracing\Zipkin\Propagation\Map;
use Yupao\Tracing\ZipkinOpenTracing\SpanContext as ZipkinOpenTracingContext;
use Yupao\Tracing\ZipkinOpenTracing\Span as ZipkinOpenTracingSpan;
use Yupao\Tracing\ZipkinOpenTracing\PartialSpanContext as ZipkinOpenPartialTracingContext;
use Yupao\Tracing\ZipkinOpenTracing\NoopSpan as ZipkinOpenTracingNoopSpan;
use Yupao\Tracing\OpenTracing\Tracer as OTTracer;
use Yupao\Tracing\OpenTracing\StartSpanOptions;
use Yupao\Tracing\OpenTracing\SpanContext as OTSpanContext;
use Yupao\Tracing\OpenTracing\Reference;
use Yupao\Tracing\OpenTracing\Formats;
use Yupao\Tracing\OpenTracing\UnsupportedFormatException;
use Yupao\Tracing\OpenTracing\Span as OTSpan;
use Yupao\Tracing\OpenTracing\Scope as OTScope;

final class Tracer implements OTTracer
{
    /**
     * @var ZipkinTracer
     */
    private $tracer;

    /**
     * @var callable[]|array
     */
    private $injectors;

    /**
     * @var callable[]|array
     */
    private $extractors;

    public function __construct(ZipkinTracing $tracing)
    {
        $propagation = $tracing->getPropagation();
        $this->injectors = [
            Formats\TEXT_MAP => $propagation->getInjector(new Map()),
            Formats\HTTP_HEADERS => $propagation->getInjector(new RequestHeaders())
        ];
        $this->extractors = [
            Formats\TEXT_MAP => $propagation->getExtractor(new Map()),
            Formats\HTTP_HEADERS => $propagation->getExtractor(new RequestHeaders())
        ];

        $this->tracer = $tracing->getTracer();
        $this->scopeManager = new ScopeManager();
    }

    /**
     * @inheritdoc
     */
    public function getScopeManager(): OTScopeManager
    {
        return $this->scopeManager;
    }

    /**
     * @inheritdoc
     */
    public function getActiveSpan(): ?OTSpan
    {
        $activeScope = $this->scopeManager->getActive();
        if ($activeScope === null) {
            return null;
        }

        return $activeScope->getSpan();
    }

    /**
     * @inheritdoc
     */
    public function startActiveSpan(string $operationName, $options = []): OTScope
    {
        if (!$options instanceof StartSpanOptions) {
            $options = StartSpanOptions::create($options);
        }

        if (!$this->hasParentInOptions($options) && $this->getActiveSpan() !== null) {
            $parent = $this->getActiveSpan()->getContext();
            $options = $options->withParent($parent);
        }

        $span = $this->startSpan($operationName, $options);
        $scope = $this->scopeManager->activate($span, $options->shouldFinishSpanOnClose());

        return $scope;
    }

    /**
     * @inheritdoc
     * @return OTSpan|Span
     */
    public function startSpan(string $operationName, $options = []): OTSpan
    {
        if (!($options instanceof StartSpanOptions)) {
            $options = StartSpanOptions::create($options);
        }

        if (!$this->hasParentInOptions($options) && $this->getActiveSpan() !== null) {
            $parent = $this->getActiveSpan()->getContext();
            $options = $options->withParent($parent);
        }

        if (empty($options->getReferences())) {
            $span = $this->tracer->newTrace();
        } else {
            /**
             * @var ZipkinOpenTracingContext $refContext
             */
            $refContext = $options->getReferences()[0]->getSpanContext();
            $context = $refContext->getContext();

            if ($context instanceof TraceContext) {
                $span = $this->tracer->newChild($context);
            } else {
                $span = $this->tracer->nextSpan($context);
            }
        }

        if ($span->isNoop()) {
            return ZipkinOpenTracingNoopSpan::create($span);
        }

        $span->start($options->getStartTime() ?: Timestamp\now());
        $span->setName($operationName);

        $otSpan = ZipkinOpenTracingSpan::create($operationName, $span, null);

        foreach ($options->getTags() as $key => $value) {
            $otSpan->setTag($key, $value);
        }

        return $otSpan;
    }

    /**
     * @inheritdoc
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function inject(OTSpanContext $spanContext, string $format, &$carrier): void
    {
        if ($spanContext instanceof ZipkinOpenTracingContext) {
            $injector = $this->getInjector($format);
            $injector($spanContext->getContext(), $carrier);
            return;
        }

        throw new \InvalidArgumentException(\sprintf(
            'Invalid span context. Expected ZipkinOpenTracing\SpanContext, got %s.',
            \is_object($spanContext) ? \get_class($spanContext) : \gettype($spanContext)
        ));
    }

    /**
     * @inheritdoc
     * @throws \UnexpectedValueException
     */
    public function extract(string $format, $carrier): ?OTSpanContext
    {
        $extractor = $this->getExtractor($format);
        $extractedContext = $extractor($carrier);

        if ($extractedContext instanceof TraceContext) {
            return ZipkinOpenTracingContext::fromTraceContext($extractedContext);
        }

        if ($extractedContext instanceof SamplingFlags) {
            return ZipkinOpenPartialTracingContext::fromSamplingFlags($extractedContext);
        }

        throw new \UnexpectedValueException(\sprintf(
            'Invalid extracted context. Expected Zipkin\SamplingFlags, got %s',
            \is_object($extractedContext) ? \get_class($extractedContext) : \gettype($extractedContext)
        ));
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        $this->tracer->flush();
    }

    /**
     * @param string $format
     * @return callable
     * @throws UnsupportedFormatException
     */
    private function getInjector($format): callable
    {
        if (array_key_exists($format, $this->injectors)) {
            return $this->injectors[$format];
        }

        throw UnsupportedFormatException::forFormat($format);
    }

    /**
     * @param string $format
     * @return callable
     * @throws UnsupportedFormatException
     */
    private function getExtractor($format): callable
    {
        if (array_key_exists($format, $this->extractors)) {
            return $this->extractors[$format];
        }

        throw UnsupportedFormatException::forFormat($format);
    }

    private function hasParentInOptions(StartSpanOptions $options): ?OTSpanContext
    {
        $references = $options->getReferences();
        foreach ($references as $ref) {
            if ($ref->isType(Reference::CHILD_OF)) {
                return $ref->getSpanContext();
            }
        }

        return null;
    }
}
