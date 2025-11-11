<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Rest\Action;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication as AuthenticationFilter;
use Bitrix\Main\Engine\ActionFilter\Csrf as CsrfFilter;
use Bitrix\Main\Engine\ActionFilter\HttpMethod as HttpMethodFilter;
use Bitrix\Main\SystemException;
use Crasivo\Pages\Domain\Api\CreatePageHtml;
use Crasivo\Pages\Domain\Api\PageRouteFinder;
use Crasivo\Pages\Domain\Api\Query\QueryFilter;
use Crasivo\Pages\Domain\Api\RawPageHtml;
use Crasivo\Pages\Network\Shared\Action\MacroConverterJson;
use Crasivo\Pages\Network\Shared\Filter\ModuleRightFilter;
use Crasivo\Pages\Network\Shared\Request\PageHtmlCreateRequest;

/**
 * @final
 * @internal
 * @restUrl /rest/crasivo:pages.route.createHtml
 */
final class PageHtmlCreateAction extends Action
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
     * @param PageHtmlCreateRequest $requestData
     * @return mixed
     * @throws \Throwable
     */
    public function run(PageHtmlCreateRequest $requestData)
    {
        $serviceLocator = ServiceLocator::getInstance();

        // check exists route
        $existsRoute = $serviceLocator
            ->get(PageRouteFinder::class)
            ->getByQueryFilter(new QueryFilter(
                routeDomain: $requestData->routeDomain,
                routePath: $requestData->routePath,
            ));
        if ($existsRoute) {
            throw new SystemException(
                sprintf(
                    'Route %s already exists.',
                    $requestData->routePath,
                ),
            );
        }

        // save via domain service
        // todo: продублировать сервис в usecase
        // todo: добавить мапперы (request>api)
        $pageRoute = $serviceLocator
            ->get(CreatePageHtml::class)
            ->do(new RawPageHtml(
                isActive: $requestData->isActive,
                routeDomain: $requestData->routeDomain,
                routePath: $requestData->routePath,
                htmlContent: $requestData->htmlContent,
                pageProperties: null,
            ));

        return $this->serializeJsonModel($pageRoute);
    }
}
