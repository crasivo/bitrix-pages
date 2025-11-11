<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
class RawPageComponent
{
    /**
     * Raw DTO constructor.
     *
     * @param bool $isActive
     * @param string|null $routeDomain
     * @param string $routePath
     * @param string $componentName
     * @param array|null $componentParams
     * @param string|null $componentTemplate
     */
    public function __construct(
        public readonly bool $isActive,
        public readonly ?string $routeDomain,
        public readonly string $routePath,
        public readonly string $componentName,
        public readonly ?array $componentParams,
        public readonly ?string $componentTemplate,
    )
    {
    }
}
