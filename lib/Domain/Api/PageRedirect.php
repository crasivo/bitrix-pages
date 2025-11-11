<?php

namespace Crasivo\Pages\Domain\Api;

interface PageRedirect extends PageRoute
{
    /**
     * Возвращает URL для переадресации на другую страницу.
     *
     * @example https://1c-bitrix.ru
     * @return string
     */
    public function getRedirectUrl(): string;
}
