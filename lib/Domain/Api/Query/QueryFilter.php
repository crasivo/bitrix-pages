<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Api\Query;

use Crasivo\Pages\Domain\Api\PageContentType;

class QueryFilter
{
    /**
     * Query DTO constructor.
     *
     * @param bool|null $isActive
     * @param PageContentType[]|null $contentTypes
     * @param string|null $routeDomain
     * @param string|null $routePath
     * @param string|null $componentName
     * @param string|null $includePath
     * @param string|null $redirectUrl
     */
    public function __construct(
        public readonly ?bool $isActive = null,
        public readonly ?array  $contentTypes = null,
        public readonly ?string $routeDomain = null,
        public readonly ?string $routePath = null,
        public readonly ?string $componentName = null,
        public readonly ?string $includePath = null,
        public readonly ?string $redirectUrl = null,
    )
    {
    }
}
