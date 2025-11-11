<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
class RawPageInclude
{
    /**
     * Raw DTO constructor.
     *
     * @param bool $isActive
     * @param string|null $routeDomain
     * @param string $routePath
     * @param string $includePath
     * @param array|null $pageProperties
     */
    public function __construct(
        public readonly bool $isActive,
        public readonly ?string $routeDomain,
        public readonly string $routePath,
        public readonly string $includePath,
        public readonly ?array $pageProperties,
    )
    {
    }
}
