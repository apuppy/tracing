<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Reporters;

use Psr\Log\LoggerInterface;
use Yupao\Tracing\Zipkin\Recording\Span;
use Yupao\Tracing\Zipkin\Reporter;

final class Log implements Reporter
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Span[] $spans
     */
    public function report(array $spans): void
    {
        foreach ($spans as $span) {
            $this->logger->info($span->__toString());
        }
    }
}
