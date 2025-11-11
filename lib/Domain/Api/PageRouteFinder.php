<?php

namespace Crasivo\Pages\Domain\Api;

use Crasivo\Pages\Domain\Api\Query\QueryFilter;
use Crasivo\Pages\Domain\Api\Query\QueryParams;

interface PageRouteFinder
{
    /**
     * @return PageRoute[]
     * @throws \Throwable
     */
    public function findAll(): iterable;

    /**
     * @return PageRoute[]
     * @throws \Throwable
     */
    public function findAllActive(): iterable;

    /**
     * @param QueryParams $queryParams
     * @return PageRoute[]
     * @throws \Throwable
     */
    public function findByQueryParams(
        QueryParams $queryParams,
    ): iterable;

    /**
     * @param int $id
     * @return PageRoute|null
     */
    public function getById(
        int $id,
    ): ?PageRoute;

    /**
     * @example /path/to/page
     * @param string $routePath
     * @return PageRoute|null
     * @throws \Throwable
     */
    public function getByRoutePath(
        string $routePath,
    ): ?PageRoute;

    /**
     * @param QueryFilter $queryFilter
     * @return PageRoute|null
     * @throws \Throwable
     */
    public function getByQueryFilter(
        QueryFilter $queryFilter,
    ): ?PageRoute;
}
