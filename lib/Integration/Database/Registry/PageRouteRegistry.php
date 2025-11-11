<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Database\Registry;

use Bitrix\Main\DI\ServiceLocator;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Api\PageRouteFinder;

/**
 * @final
 * @internal
 */
class PageRouteRegistry implements \Crasivo\Pages\Domain\Registry\PageRouteRegistry
{
    /** @var PageRoute[] */
    protected array $pageRoutes = [];

    /** @var PageRouteRegistry|null */
    private static ?self $instance = null;

    /**
     * Registry constructor.
     *
     * @throws \Throwable
     */
    public function __construct()
    {
        $this->pageRoutes = ServiceLocator::getInstance()
            ->get(PageRouteFinder::class)
            ->findAllActive();
    }

    /**
     * @return self
     * @throws \Throwable
     */
    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->pageRoutes;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->pageRoutes);
    }

    /**
     * @inheritDoc
     */
    public function hasPath(string $path): bool
    {
        return isset($this->pageRoutes[$path]);
    }

    /**
     * @inheritDoc
     */
    public function getPath(string $path): ?PageRoute
    {
        foreach ($this->pageRoutes as $route) {
            if ($route->getRoutePath() === $path) {
                return $route;
            }
        }

        return null;
    }
}
