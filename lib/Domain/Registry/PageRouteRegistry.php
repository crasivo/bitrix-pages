<?php

namespace Crasivo\Pages\Domain\Registry;

use Crasivo\Pages\Domain\Api\PageRoute;

interface PageRouteRegistry extends \Countable
{
    /**
     * Возвращает полную коллекцию страниц (маршрутов).
     *
     * @return iterable
     */
    public function all(): iterable;

    /**
     * Возвращает флаг наличия указанного пути (маршрута).
     *
     * @param string $path
     * @return bool
     */
    public function hasPath(string $path): bool;

    /**
     * Возвращает страницу (маршрут) по наименованию пути.
     *
     * @example /path/to/page
     * @param string $path
     * @return PageRoute|null
     */
    public function getPath(string $path): ?PageRoute;
}
