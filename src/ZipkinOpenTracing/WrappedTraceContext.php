<?php

namespace Yupao\Tracing\ZipkinOpenTracing;

use Yupao\Tracing\Zipkin\Propagation\SamplingFlags;

interface WrappedTraceContext
{
    /**
     * @return SamplingFlags
     */
    public function getContext();
}
