<?php

namespace Yupao\Tracing\Zipkin;

use Yupao\Tracing\Zipkin\Tags;
use Throwable;

class DefaultErrorParser implements ErrorParser
{
    public function parseTags(Throwable $e): array
    {
        return [Tags\ERROR => $e->getMessage()];
    }
}
