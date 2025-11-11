<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Request;

use Bitrix\Main\Validation\Rule\NotEmpty;
use Crasivo\Pages\Integration\Validation\Rule\Domain;
use Crasivo\Pages\Integration\Validation\Rule\ScalarKeyValue;
use Crasivo\Pages\Integration\Validation\Rule\UriPath;

/**
 * @final
 * @internal
 */
class PageIncludeUpdateRequest extends PageRouteRequest
{
    /**
     * Request DTO constructor.
     *
     * @param bool $isActive
     * @param string|null $routeDomain
     * @param string $routePath
     * @param string $includePath
     * @param array|null $pageProperties
     */
    public function __construct(
        public readonly bool $isActive,
        #[Domain]
        public readonly ?string $routeDomain,
        #[UriPath]
        public readonly string $routePath,
        #[NotEmpty]
        public readonly string $includePath,
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
            includePath: is_string($data['includePath']) ? $data['includePath'] : '',
            pageProperties: is_array($data['pageProperties']) ? $data['pageProperties'] : null,
        );
    }
}
