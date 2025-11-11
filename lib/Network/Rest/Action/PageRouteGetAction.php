<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Rest\Action;

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication as AuthenticationFilter;
use Bitrix\Main\Engine\ActionFilter\Csrf as CsrfFilter;
use Bitrix\Main\Engine\ActionFilter\HttpMethod as HttpMethodFilter;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Network\Shared\Action\MacroConverterJson;
use Crasivo\Pages\Network\Shared\Filter\ModuleRightFilter;

/**
 * @internal
 * @restUrl /rest/crasivo:page.route.createComponent
 */
final class PageRouteGetAction extends Action
{
    use MacroConverterJson;

    /**
     * Returns the settings for the controller action.
     *
     * @return array[]
     */
    public static function getSettings(): array
    {
        return [
            'class' => __CLASS__,
            '-prefilters' => [
                CsrfFilter::class,
            ],
            '+prefilters' => [
                new AuthenticationFilter(),
                new HttpMethodFilter([
                    HttpMethodFilter::METHOD_GET,
                ]),
                new ModuleRightFilter(ModuleRightFilter::READ),
            ],
        ];
    }

    /**
     * @param PageRoute $pageRoute
     * @return array
     * @throws \Throwable
     */
    public function run(
        PageRoute $pageRoute,
    ): array
    {
        return $this->serializeJsonModel($pageRoute);
    }
}
