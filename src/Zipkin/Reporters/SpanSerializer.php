<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Reporters;

use Yupao\Tracing\Zipkin\Recording\ReadbackSpan;

/**
 * SpanSerializer turns a list of spans into a series of
 * bytes (byte = character).
 */
interface SpanSerializer
{
    /**
     * @param ReadbackSpan[]|array $spans
     * @return string with spans serialized
     */
    public function serialize(array $spans): string;
}
