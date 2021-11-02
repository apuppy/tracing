<?php

declare(strict_types=1);

namespace Yupao\Tracing\Zipkin\Recording;

use Yupao\Tracing\Zipkin\Reporters\SpanSerializer;
use Yupao\Tracing\Zipkin\ErrorParser;
use Yupao\Tracing\Zipkin\Endpoint;
use Throwable;

/**
 * ReadbackSpan is an interface for accessing the recording
 * span without the possibility to mutate it.
 */
interface ReadbackSpan
{
    public function getSpanId(): string;
    public function getTraceId(): string;
    public function getParentId(): ?string;
    public function isDebug(): bool;
    public function isShared(): bool;
    public function getName(): ?string;
    public function getKind(): ?string;
    public function getTimestamp(): int;
    public function getDuration(): ?int;
    public function getLocalEndpoint(): ?Endpoint;
    public function getTags(): array;
    public function getAnnotations(): array;
    public function getError(): ?Throwable;
    public function getRemoteEndpoint(): ?Endpoint;
}
