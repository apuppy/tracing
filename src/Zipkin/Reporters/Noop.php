<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Reporters;

use Yupao\Tracing\Zipkin\Recording\Span;
use Yupao\Tracing\Zipkin\Reporter;

final class Noop implements Reporter
{
    /**
     * @param Span[] $spans
     * @return void
     */
    public function report(array $spans): void
    {
    }
}
