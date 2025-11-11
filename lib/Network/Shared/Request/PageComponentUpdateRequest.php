<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Request;

use Crasivo\Pages\Integration\Validation\Rule\ComponentName;
use Crasivo\Pages\Integration\Validation\Rule\ComponentTemplate;
use Crasivo\Pages\Integration\Validation\Rule\Domain;
use Crasivo\Pages\Integration\Validation\Rule\ScalarKeyValue;
use Crasivo\Pages\Integration\Validation\Rule\UriPath;

/**
 * @final
 * @internal
 */
class PageComponentUpdateRequest extends PageRouteRequest
{
    /**
     * Request DTO constructor.
     *
     * @param bool $isActive
     * @param string $componentName
     * @param array|null $componentParams
     * @param string|null $componentTemplate
     * @param array|null $pageProperties
     * @param string|null $routeDomain
     * @param string $routePath
     */
    public function __construct(
        public readonly bool $isActive,
        #[ComponentName]
        public readonly string $componentName,
        #[ScalarKeyValue]
        public readonly ?array $componentParams = null,
        #[ComponentTemplate]
        public readonly ?string $componentTemplate = null,
        #[ScalarKeyValue]
        public readonly ?array $pageProperties = null,
        #[Domain]
        public readonly ?string $routeDomain = null,
        #[UriPath]
        public readonly string $routePath,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public static function fromRequestData(array $data): static
    {
        return new self(
            isActive: isset($data['isActive']) && in_array($data['isActive'], [true, 'Y']),
            componentName: is_string($data['componentName']) ? $data['componentName'] : null,
            componentParams: is_array($data['componentParams']) ? $data['componentParams'] : null,
            componentTemplate: is_string($data['componentTemplate']) ? $data['componentTemplate'] : null,
            pageProperties: is_array($data['pageProperties']) ? $data['pageProperties'] : null,
            routeDomain: is_string($data['routeDomain']) ? $data['routeDomain'] : null,
            routePath: is_string($data['routePath']) ? $data['routePath'] : '',
        );
    }
}
