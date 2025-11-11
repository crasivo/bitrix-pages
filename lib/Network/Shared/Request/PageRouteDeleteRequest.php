<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Request;

/**
 * @final
 * @internal
 */
class PageRouteDeleteRequest extends PageRouteRequest
{
    /**
     * Request DTO constructor.
     *
     * @param string|null $routePath
     * @param string|null $routeDomain
     */
    public function __construct(
        public readonly ?string $routePath,
        public readonly ?string $routeDomain,
    )
    {
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromRequestData(array $data): static
    {
        return new self(
            routePath: is_string($data['routePath']) ? $data['routePath'] : null,
            routeDomain: is_string($data['routeDomain']) ? $data['routeDomain'] : null,
        );
    }
}
