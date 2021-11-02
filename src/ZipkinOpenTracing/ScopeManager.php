<?php

namespace Yupao\Tracing\ZipkinOpenTracing;

use Yupao\Tracing\OpenTracing\Span as OTSpan;
use Yupao\Tracing\OpenTracing\ScopeManager as OTScopeManager;
use Yupao\Tracing\OpenTracing\Scope as OTScope;

final class ScopeManager implements OTScopeManager
{
    /**
     * @var Scope|null
     */
    private $active;

    /**
     * {@inheritdoc}
     */
    public function activate(OTSpan $span, bool $finishSpanOnClose = true): OTScope
    {
        // restorer allows to the scope to restore a parent scope without the
        // scope manager to expose a specific method for it.
        $restorer = function (?Scope $scope): void {
            $this->active = $scope;
        };

        $this->active = new Scope($this, $span, $finishSpanOnClose, $this->active, $restorer);
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function getActive(): ?OTScope
    {
        return $this->active;
    }
}
