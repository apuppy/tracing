<?php

declare(strict_types=1);

namespace Yupao\Tracing\OpenTracing\Mock;

use Yupao\Tracing\OpenTracing\Scope;
use Yupao\Tracing\OpenTracing\ScopeManager;
use Yupao\Tracing\OpenTracing\Span;

final class MockScopeManager implements ScopeManager
{
    /**
     * @var Scope[]
     */
    private $scopes = [];

    /**
     * {@inheritdoc}
     */
    public function activate(Span $span, bool $finishSpanOnClose = ScopeManager::DEFAULT_FINISH_SPAN_ON_CLOSE): Scope
    {
        $scope = new MockScope($this, $span, $finishSpanOnClose);
        $this->scopes[] = $scope;

        return $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getActive(): ?Scope
    {
        if (empty($this->scopes)) {
            return null;
        }

        return $this->scopes[count($this->scopes) - 1];
    }

    public function deactivate(MockScope $scope): void
    {
        foreach ($this->scopes as $scopeIndex => $scopeItem) {
            if ($scope === $scopeItem) {
                unset($this->scopes[$scopeIndex]);
            }
        }
    }
}
