<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Model;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Validation\Rule\Min;
use Crasivo\Pages\Domain\Api\PageContentType;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Api\PageRouteFinder;
use Crasivo\Pages\Domain\Exception\PageRouteNotFoundException;
use Crasivo\Pages\Integration\Validation\Rule\Domain;
use Crasivo\Pages\Integration\Validation\Rule\ScalarKeyValue;
use Crasivo\Pages\Integration\Validation\Rule\UriPath;

abstract class PageRouteModel implements PageRoute
{
    #[Min(0)]
    private int $id = 0;

    /** @var bool */
    protected bool $isActive = false;

    #[ScalarKeyValue]
    protected array $pageProperties = [];

    #[Domain]
    protected string $routeDomain = 'localhost';

    #[UriPath]
    protected string $routePath;

    /** @var string|null */
    protected ?string $viewPath = null;

    /**
     * NOTES:
     * - на данный момент хранилище для всех маршрутов (страниц) единое, поэтому метод универсальный
     *
     * @inheritDoc
     * @throws \Throwable
     */
    public static function createFromId(int $itemId): static
    {
        return ServiceLocator::getInstance()
            ->get(PageRouteFinder::class)
            ->getById($itemId)
            ?? throw new PageRouteNotFoundException(sprintf(
                'Page route #%d not found.',
                $itemId,
            ));
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): PageContentType
    {
        return PageContentType::Custom;
    }

    /**
     * @inheritDoc
     */
    final public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getPageProperty(
        string $key,
        ?string $default = null,
    ): ?string
    {
        return $this->pageProperties[$key] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function getRouteDomain(): string
    {
        return $this->routeDomain;
    }

    /**
     * @inheritDoc
     */
    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    /**
     * @inheritDoc
     */
    public function getViewPath(): ?string
    {
        return $this->viewPath;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $flag
     * @return void
     */
    public function setActive(bool $flag): void
    {
        $this->isActive = $flag;
    }

    /**
     * @internal
     * @param int $id
     * @return void
     */
    final public function setId(int $id): void
    {
        if ($id < 0) {
            throw new \InvalidArgumentException('ID cannot be negative.');
        }

        $this->id = $id;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setPageProperty(
        string $key,
        string $value,
    ): void
    {
        $this->pageProperties[$key] = $value;
    }

    /**
     * @param string|null $domain
     * @return void
     */
    public function setRouteDomain(?string $domain): void
    {
        $this->routeDomain = $domain ?? 'localhost';
    }

    /**
     * @param string $routePath
     * @return void
     */
    public function setRoutePath(string $routePath): void
    {
        $this->routePath = $routePath;
    }

    /**
     * @param string|null $viewPath
     * @return void
     */
    public function setViewPath(?string $viewPath): void
    {
        $this->viewPath = $viewPath;
    }

    /**
     * @return void
     */
    public function __clone(): void
    {
        $this->id = 0;
    }
}
