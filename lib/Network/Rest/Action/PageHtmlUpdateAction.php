<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Rest\Action;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication as AuthenticationFilter;
use Bitrix\Main\Engine\ActionFilter\Csrf as CsrfFilter;
use Bitrix\Main\Engine\ActionFilter\HttpMethod as HttpMethodFilter;
use Bitrix\Main\SystemException;
use Crasivo\Pages\Domain\Api\PageHtml;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Api\RawPageHtml;
use Crasivo\Pages\Domain\Api\UpdatePageHtml;
use Crasivo\Pages\Network\Shared\Action\MacroConverterJson;
use Crasivo\Pages\Network\Shared\Filter\ModuleRightFilter;
use Crasivo\Pages\Network\Shared\Request\PageHtmlUpdateRequest;

/**
 * @final
 * @internal
 * @restUrl /rest/crasivo:pages.route.updateHtml
 */
final class PageHtmlUpdateAction extends Action
{
    use MacroConverterJson;

    /**
     * Returns the settings for the controller action.
     *
     * @return array
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
     * Execute controller action.
     *
     * @param PageRoute $pageRoute
     * @param PageHtmlUpdateRequest $requestData
     * @return mixed
     * @throws \Throwable
     */
    public function run(
        PageRoute $pageRoute,
        PageHtmlUpdateRequest $requestData,
    )
    {
        // check content type
        if (!($pageRoute instanceof PageHtml)) {
            throw new SystemException(
                sprintf(
                    'Route %s has a different content type (%s).',
                    $requestData->routePath,
                    $pageRoute->getContentType()->value,
                ),
            );
        }

        // save via domain service
        // todo: продублировать сервис в usecase
        // todo: добавить мапперы (request>api)
        $pageRoute = ServiceLocator::getInstance()
            ->get(UpdatePageHtml::class)
            ->do(
                pageHtml: $pageRoute,
                rawPageHtml: new RawPageHtml(
                    isActive: $requestData->isActive,
                    routeDomain: $requestData->routeDomain,
                    routePath: $requestData->routePath,
                    htmlContent: $requestData->htmlContent,
                    pageProperties: null,
                ),
            );

        return $this->serializeJsonModel($pageRoute);
    }
}
