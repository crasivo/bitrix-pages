<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Routing;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Routing\RoutingConfigurator;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Registry\PageRouteRegistry;
use Crasivo\Pages\Network\Web\Controller\PublicController;

/**
 * @final
 * @internal
 */
final class RouteContainer implements \Psr\Container\ContainerInterface
{
    /** @var RouteContainer|null */
    private static ?self $instance = null;

    /** @var PageRouteRegistry */
    private PageRouteRegistry $pageRouteRegistry;

    /**
     * Container constructor.
     *
     * @throws \Throwable
     */
    public function __construct()
    {
        $this->pageRouteRegistry = ServiceLocator::getInstance()
            ->get(PageRouteRegistry::class);
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return $this->pageRouteRegistry->hasPath($id);
    }

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        return $this->pageRouteRegistry->getPath($id);
    }

    /**
     * @return RouteContainer
     */
    public static function getInstance(): RouteContainer
    {
        return self::$instance ??= new self();
    }

    /**
     * @param RoutingConfigurator $routingConfigurator
     * @return void
     */
    public function injectConfigurator(RoutingConfigurator $routingConfigurator): void
    {
        /** @var PageRoute[] $pageRoutes */
        $pageRoutes = $this->pageRouteRegistry->all();
        foreach ($pageRoutes as $pageRoute) {
            $routingConfigurator
                ->any($pageRoute->getRoutePath(), [PublicController::class, 'show'])
                ->domain($pageRoute->getRouteDomain())
                ->name('crasivo_pages_' . $pageRoute?->getId());
        }
    }
}
