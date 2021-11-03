<?php
declare(strict_types=1);

namespace Yupao\Tracing\Support\Helpers;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;

/**
 * @return string
 */
function getCustomizedTraceID(): string
{
    $traceIDName = env('CUSTOMIZED_TRACE_ID_NAME', 'req_id');
    /** @var $request RequestInterface */
    $request = ApplicationContext::getContainer()->get(RequestInterface::class);
    $traceId = '';
    if ($request->hasHeader($traceIDName)) {
        $traceId = (string)$request->header($traceIDName, '');
    }
    return $traceId;
}