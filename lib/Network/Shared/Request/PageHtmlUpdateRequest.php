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
class PageHtmlUpdateRequest extends PageRouteRequest
{
    /**
     * Request DTO constructor.
     *
     * @param bool $isActive
     * @param string|null $routeDomain
     * @param string $routePath
     * @param string $htmlContent
     * @param array|null $pageProperties
     */
    public function __construct(
        public bool $isActive,
        #[Domain]
        public readonly ?string $routeDomain,
        #[UriPath]
        public readonly string $routePath,
        #[NotEmpty]
        public readonly string $htmlContent,
        #[ScalarKeyValue]
        public readonly ?array $pageProperties,
    )
    {
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromRequestData(array $data): static
    {
        return new static(
            isActive: isset($data['isActive']) && in_array($data['isActive'], [true, 'Y']),
            routeDomain: is_string($data['routeDomain']) ? $data['routeDomain'] : null,
            routePath: is_string($data['routePath']) ? $data['routePath'] : '',
            htmlContent: is_string($data['htmlContent']) ? $data['htmlContent'] : '',
            pageProperties: is_array($data['pageProperties']) ? $data['pageProperties'] : null,
        );
    }
}
