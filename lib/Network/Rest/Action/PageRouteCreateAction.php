<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Rest\Action;

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication as AuthenticationFilter;
use Bitrix\Main\Engine\ActionFilter\Csrf as CsrfFilter;
use Bitrix\Main\Engine\ActionFilter\HttpMethod as HttpMethodFilter;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\HttpResponse;
use Crasivo\Pages\Network\Shared\Filter\ModuleRightFilter;
use Crasivo\Pages\Network\Shared\Request\PageRouteCreateRequest;

/**
 * @final
 * @internal
 * @restUrl /rest/crasivo:pages.route.create
 */
class PageRouteCreateAction extends Action
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
                    HttpMethodFilter::METHOD_POST,
                    HttpMethodFilter::METHOD_PUT,
                ]),
                new ModuleRightFilter(ModuleRightFilter::WRITE),
            ],
        ];
    }

    /**
     * Execute the controller action.
     *
     * @param PageRouteCreateRequest $requestData
     * @return HttpResponse
     */
    public function run(PageRouteCreateRequest $requestData): HttpResponse
    {
        return AjaxJson::createSuccess();
    }
}
