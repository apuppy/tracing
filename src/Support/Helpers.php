<?php
declare(strict_types=1);

namespace Yupao\Tracing\Support\Helpers;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;

/**
 * @return string
 */
function getReqId(): string
{
    /** @var $request RequestInterface */
    $request = ApplicationContext::getContainer()->get(RequestInterface::class);
    $traceId = '';
    if ($request->hasHeader('req_id')) {
        $traceId = (string) $request->header('req_id', '');
    }
    return $traceId;
}