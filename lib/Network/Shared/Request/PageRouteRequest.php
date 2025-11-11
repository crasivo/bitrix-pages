<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Request;

use Bitrix\Main\Context;
use Bitrix\Main\HttpRequest;

/**
 * @internal
 */
abstract class PageRouteRequest
{
    /**
     * @param array $data
     * @return mixed
     */
    abstract public static function fromRequestData(array $data);

    /**
     * @param HttpRequest|null $request
     * @return mixed
     */
    public static function fromRequest(?HttpRequest $request = null)
    {
        if (!$request) {
            $request = Context::getCurrent()->getRequest();
        }
        if ($request->isJson()) {
            return static::fromRequestData($request->getJsonList()->getValues());
        }

        return static::fromRequestData($request->getValues());
    }
}
