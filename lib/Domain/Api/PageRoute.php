<?php

namespace Crasivo\Pages\Domain\Api;

use Bitrix\Main\Access\AccessibleItem;

interface PageRoute extends AccessibleItem
{
    /**
     * Возвращает тип контента страницы (маршрута).
     *
     * @return PageContentType
     */
    public function getContentType(): PageContentType;

    /**
     * Возвращает свойство страницы (маршрута).
     *
     * @see \CMain::SetPageProperty
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function getPageProperty(string $key, ?string $default = null): ?string;

    /**
     * Возвращает доменное имя для маршрута.
     *
     * @see \Bitrix\Main\Routing\Route::match
     * @return string|null
     */
    public function getRouteDomain(): ?string;

    /**
     * Возвращает путь к маршруту.
     *
     * @return string
     * @example /some/path
     */
    public function getRoutePath(): string;

    /**
     * Возвращает путь к файлу, отвечающему за вывод содержимого (MVC).
     *
     * @example /local/views/custom.php
     * @link https://docs.1c-bitrix.ru/pages/framework/controllers.html
     * @return string|null
     */
    public function getViewPath(): ?string;

    /**
     * Возвращает флаг активности страницы (маршрута).
     *
     * @return bool
     */
    public function isActive(): bool;
}
