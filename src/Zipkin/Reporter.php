<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin;

use Yupao\Tracing\Zipkin\Recording\Span as MutableSpan;

interface Reporter
{
    /**
     * @param MutableSpan[] $spans
     * @return void
     */
    public function report(array $spans): void;
}
