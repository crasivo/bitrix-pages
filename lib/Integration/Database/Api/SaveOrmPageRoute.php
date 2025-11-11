<?php

namespace Crasivo\Pages\Integration\Database\Api;

use Crasivo\Pages\Domain\Api\PageRoute;

/**
 * NOTES:
 * - это инфраструктурный (внутренний) сервис, не надо его дергать без необходимости
 *
 * @final
 * @internal
 */
interface SaveOrmPageRoute
{
    /**
     * Execute service action.
     *
     * @param array $ormData
     * @return PageRoute
     * @throws \Throwable
     */
    public function do(
        array $ormData,
    ): PageRoute;
}
