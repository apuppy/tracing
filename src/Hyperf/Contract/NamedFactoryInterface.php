<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Yupao\Tracing\Hyperf\Contract;

interface NamedFactoryInterface
{
    /**
     * Create the object from factory.
     */
    public function make(string $name): \Yupao\Tracing\OpenTracing\Tracer;
}
