<?php

namespace Yupao\Tracing\ZipkinOpenTracing;

use Yupao\Tracing\OpenTracing\Span as OTSpan;
use Yupao\Tracing\OpenTracing\Scope as OTScope;

final class Scope implements OTScope
{
    /**
     * @var ScopeManager
     */
    private $scopeManager;

    /**
     * @var OTSpan
     */
    private $wrapped;

    /**
     * @var bool
     */
    private $finishSpanOnClose;

    /**
     * @var Scope
     */
    private $toRestore;

    /**
     * @var bool
     */
    private $isClosed = false;

    /**
     * @var callable|function(?Scope):void
     */
    private $restorer;

    public function __construct(
        ScopeManager $scopeManager,
        OTSpan $wrapped,
        bool $finishSpanOnClose,
        ?Scope $toRestore,
        callable $restorer
    ) {
        $this->scopeManager = $scopeManager;
        $this->wrapped = $wrapped;
        $this->finishSpanOnClose = $finishSpanOnClose;
        $this->toRestore = $toRestore;
        $this->restorer = $restorer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpan(): OTSpan
    {
        return $this->wrapped;
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        if ($this->isClosed) {
            return;
        }

        if ($this->finishSpanOnClose) {
            $this->wrapped->finish();
        }

        $this->isClosed = true;
        if ($this->scopeManager->getActive() !== $this) {
            // This shouldn't happen if users call methods in expected order
            return;
        }

        if ($this->toRestore === null) {
            ($this->restorer)(null);
            return;
        }

        $toRestore = $this->toRestore;
        while (true) {
            // If the toRestore scope is already closed, we want to go up
            // to the previous level recursively until we get to the last
            // first one that is still open.
            if ($toRestore->isClosed) {
                $toRestore = $toRestore->toRestore;
            } else {
                break;
            }

            if ($toRestore === null) {
                ($this->restorer)(null);
                return;
            }
        }

        ($this->restorer)($toRestore);
    }
}
