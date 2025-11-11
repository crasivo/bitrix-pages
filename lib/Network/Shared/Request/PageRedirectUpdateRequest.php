<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Request;

use Bitrix\Main\Validation\Rule\Url;
use Crasivo\Pages\Integration\Validation\Rule\Domain;
use Crasivo\Pages\Integration\Validation\Rule\ScalarKeyValue;
use Crasivo\Pages\Integration\Validation\Rule\UriPath;

/**
 * @final
 * @internal
 */
class PageRedirectUpdateRequest extends PageRouteRequest
{
    /**
     * Request DTO constructor.
     *
     * @param bool $isActive
     * @param string|null $routeDomain
     * @param string $routePath
     * @param string $redirectUrl
     * @param string[]|null $pageProperties
     */
    public function __construct(
        public readonly bool $isActive,
        #[Domain]
        public readonly ?string $routeDomain,
        #[UriPath]
        public readonly string $routePath,
        #[Url]
        public readonly string $redirectUrl,
        #[ScalarKeyValue]
        public readonly ?array $pageProperties,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public static function fromRequestData(array $data): static
    {
        return new static(
            isActive: isset($data['isActive']) && in_array($data['isActive'], [true, 'Y']),
            routeDomain: is_string($data['routeDomain']) ? $data['routeDomain'] : null,
            routePath: is_string($data['routePath']) ? $data['routePath'] : '',
            redirectUrl: is_string($data['redirectUrl']) ? $data['redirectUrl'] : null,
            pageProperties: is_array($data['pageProperties']) ? $data['pageProperties'] : null,
        );
    }
}
