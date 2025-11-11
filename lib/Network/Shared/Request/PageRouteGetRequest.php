<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Request;

use Bitrix\Main\Validation\Rule\AtLeastOnePropertyNotEmpty;
use Crasivo\Pages\Integration\Validation\Rule\UriPath;

#[AtLeastOnePropertyNotEmpty(['routePath', 'routeId'])]
class PageRouteGetRequest extends PageRouteRequest
{
    /**
     * Request DTO constructor.
     *
     * @param string|null $routePath
     * @param string|null $routeDomain
     */
    public function __construct(
        #[UriPath]
        public readonly ?string $routePath,
        public readonly ?string $routeDomain,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public static function fromRequestData(array $data): static
    {
        return new self(
            routePath: is_string($data['routePath']) ? $data['routePath'] : null,
            routeDomain: is_string($data['routeDomain']) ? $data['routeDomain'] : null,
        );
    }
}
