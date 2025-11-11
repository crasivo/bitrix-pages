<?php

namespace Crasivo\Pages\Domain\Repository;

use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Api\PageRouteFinder;

interface PageRouteRepository extends PageRouteFinder
{
    /**
     * Сохранение маршрута в БД.
     *
     * @param PageRoute $pageRoute
     * @return mixed|void
     * @throws \Throwable
     */
    public function put(
        PageRoute $pageRoute,
    );

    /**
     * Полное удаление маршрута из БД.
     *
     * @param PageRoute $pageRoute
     * @return mixed|void
     * @throws \Throwable
     */
    public function remove(
        PageRoute $pageRoute,
    );
}
