<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Rest\Action;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Authentication as AuthenticationFilter;
use Bitrix\Main\Engine\ActionFilter\Csrf as CsrfFilter;
use Bitrix\Main\Engine\ActionFilter\HttpMethod as HttpMethodFilter;
use Bitrix\Main\SystemException;
use Crasivo\Pages\Domain\Api\CreatePageComponent;
use Crasivo\Pages\Domain\Api\PageRouteFinder;
use Crasivo\Pages\Domain\Api\Query\QueryFilter;
use Crasivo\Pages\Domain\Api\RawPageComponent;
use Crasivo\Pages\Network\Shared\Action\MacroConverterJson;
use Crasivo\Pages\Network\Shared\Filter\ModuleRightFilter;
use Crasivo\Pages\Network\Shared\Request\PageComponentCreateRequest;

/**
 * @final
 * @internal
 * @restUrl /rest/crasivo:page.route.createComponent
 */
class PageComponentCreateAction extends Action
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
     * @param PageComponentCreateRequest $requestData
     * @return mixed
     * @throws \Throwable
     */
    public function run(
        PageComponentCreateRequest $requestData,
    )
    {
        $serviceLocator = ServiceLocator::getInstance();

        // check route exists
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
        $pageComponent = $serviceLocator
            ->get(CreatePageComponent::class)
            ->do(new RawPageComponent(
                isActive: $requestData->isActive,
                routeDomain: $requestData->routeDomain,
                routePath: $requestData->routePath,
                componentName: $requestData->componentName,
                componentParams: $requestData->componentParams,
                componentTemplate: $requestData->componentTemplate,
            ));

        return $this->serializeJsonModel($pageComponent);
    }
}
