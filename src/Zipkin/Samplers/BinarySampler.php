<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Samplers;

use Yupao\Tracing\Zipkin\Sampler;

final class BinarySampler implements Sampler
{
    /**
     * @var bool
     */
    private $isSampled;

    private function __construct(bool $isSampled)
    {
        $this->isSampled = $isSampled;
    }

    public static function createAsAlwaysSample(): self
    {
        return new self(true);
    }

    public static function createAsNeverSample(): self
    {
        return new self(false);
    }

    /**
     * {@inheritdoc}
     */
    public function isSampled(string $traceId): bool
    {
        return $this->isSampled;
    }
}
