<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Propagation;

interface SamplingFlags
{
    const EMPTY_SAMPLED = null;
    const EMPTY_DEBUG = false;

    public function isSampled(): ?bool;

    public function isDebug(): bool;

    public function isEmpty(): bool;

    public function isEqual(SamplingFlags $samplingFlags): bool;

    public function withSampled(bool $isSampled): SamplingFlags;
}
