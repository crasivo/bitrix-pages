<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
class RawPageRedirect
{
    /**
     * Raw DTO constructor.
     *
     * @param bool $isActive
     * @param string|null $routeDomain
     * @param string $routePath
     * @param string $redirectUrl
     * @param RawPageProperty[]|null $pageProperties
     */
    public function __construct(
        public readonly bool $isActive,
        public readonly ?string $routeDomain,
        public readonly string $routePath,
        public readonly string $redirectUrl,
        public readonly ?array $pageProperties,
    )
    {
    }
}
