<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Rest\Action;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication as AuthenticationFilter;
use Bitrix\Main\Engine\ActionFilter\Csrf as CsrfFilter;
use Bitrix\Main\Engine\ActionFilter\HttpMethod as HttpMethodFilter;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Api\RemovePageRoute;
use Crasivo\Pages\Network\Shared\Filter\ModuleRightFilter;

/**
 * @final
 * @internal
 * @restUrl /rest/crasivo:pages.route.delete
 */
class PageRouteDeleteAction extends Action
{
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
                    HttpMethodFilter::METHOD_DELETE,
                    HttpMethodFilter::METHOD_POST,
                ]),
                new ModuleRightFilter(ModuleRightFilter::WRITE),
            ],
        ];
    }

    /**
     * Execute controller action.
     *
     * @param PageRoute $pageRoute
     * @return mixed
     * @throws \Throwable
     */
    public function run(
        PageRoute $pageRoute,
    )
    {
        // save via domain service
        // todo: продублировать сервис в usecase
        ServiceLocator::getInstance()
            ->get(RemovePageRoute::class)
            ->do($pageRoute);

        return true;
    }
}
