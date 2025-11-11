<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Database\Repository;

use Bitrix\Main\ORM\Query\Query;
use Crasivo\Pages\Domain\Api\PageContentType;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Api\Query\QueryFilter;
use Crasivo\Pages\Domain\Api\Query\QueryParams;
use Crasivo\Pages\Integration\Database\ORM\PageRouteTable;

/**
 * @final
 * @internal
 */
class PageRouteRepository implements \Crasivo\Pages\Domain\Repository\PageRouteRepository
{
    /** @var self|null */
    private static ?self $instance = null;

    /**
     * Singleton initializer.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function findAll(): array
    {
        $query = PageRouteTable::query();
        $query->addSelect('*');

        return array_map(
            fn($r) => PageRouteTable::convertRowToModel($r),
            $query->fetchAll(),
        );
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function findAllActive(): array
    {
        $query = PageRouteTable::query();
        $query->addSelect('*');
        $query->where(PageRouteTable::COLUMN_ACTIVE, '=', true);

        return array_map(
            fn($r) => PageRouteTable::convertRowToModel($r),
            $query->fetchAll(),
        );
    }

    /**
     * @inheritDoc
     */
    public function findByQueryParams(QueryParams $queryParams): array
    {
        return array_map(
            fn($r) => PageRouteTable::convertRowToModel($r),
            static::buildQueryFromParams($queryParams)->fetchAll(),
        );
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function getById(int $id): ?PageRoute
    {
        $query = PageRouteTable::query();
        $query->where(PageRouteTable::COLUMN_ID, '=', $id);
        $query->setLimit(1);
        $query->addSelect('*');
        $model = $query->fetch();
        if (!is_array($model)) {
            return null;
        }

        return PageRouteTable::convertRowToModel($model);
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    public function getByQueryFilter(QueryFilter $queryFilter): ?PageRoute
    {
        $query = static::buildQueryFromFilter($queryFilter);
        $query->addSelect('*');
        $query->setLimit(1);
        $model = $query->fetch();
        if (!is_array($model)) {
            return null;
        }

        return PageRouteTable::convertRowToModel($model);
    }

    /**
     * @inheritDoc
     */
    public function getByRoutePath(
        string $routePath,
    ): ?PageRoute
    {
        return static::getByQueryFilter(new QueryFilter(
            routePath: $routePath,
        ));
    }

    /**
     * @inheritDoc
     */
    public function put(PageRoute $pageRoute)
    {
        if ($pageRoute->getId() > 0) {
            PageRouteTable::updateModel($pageRoute);
        } else {
            PageRouteTable::createModel($pageRoute);
        }

        PageRouteTable::cleanCache();
    }

    /**
     * @inheritDoc
     */
    public function remove(PageRoute $pageRoute)
    {
        PageRouteTable::deleteModel($pageRoute);
        PageRouteTable::cleanCache();
    }

    /**
     * @param QueryFilter $queryFilter
     * @return Query
     * @throws \Throwable
     */
    protected function buildQueryFromFilter(QueryFilter $queryFilter): Query
    {
        $queryBuilder = PageRouteTable::query();
        if (is_bool($queryFilter->isActive)) {
            $queryBuilder->where(PageRouteTable::COLUMN_ACTIVE, '=', $queryFilter->isActive);
        }
//        if (is_string($queryFilter->routeDomain)) {
//            $queryBuilder->where(PageRouteTable::COLUMN_ROUTE_DOMAIN, '=', $queryFilter->routeDomain);
//        }
        if (is_string($queryFilter->routePath)) {
            $queryBuilder->where(PageRouteTable::COLUMN_ROUTE_PATH, '=', $queryFilter->routePath);
        }
        if (is_string($queryFilter->componentName)) {
            $queryBuilder->where(PageRouteTable::COLUMN_CONTENT_TYPE, '=', PageContentType::Component->value);
            $queryBuilder->where(PageRouteTable::COLUMN_COMPONENT_TEMPLATE, '=', $queryFilter->componentName);
        }
        if (is_string($queryFilter->redirectUrl)) {
            $queryBuilder->where(PageRouteTable::COLUMN_CONTENT_TYPE, '=', PageContentType::Redirect->value);
            $queryBuilder->whereLike(PageRouteTable::COLUMN_REDIRECT_URL, $queryFilter->redirectUrl);
        }

        return $queryBuilder;
    }

    /**
     * @param QueryParams $queryParams
     * @return Query
     * @throws \Throwable
     */
    protected function buildQueryFromParams(QueryParams $queryParams): Query
    {
        $queryBuilder = $queryParams->filter
            ? static::buildQueryFromFilter($queryParams->filter)
            : PageRouteTable::query();
        $queryBuilder->addSelect(['*']);
        $queryBuilder->setCacheTtl(86400);
        $queryBuilder->setLimit($queryParams->limit);
        $queryBuilder->setOffset($queryParams->offset);

        return $queryBuilder;
    }
}
