<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Web\Controller;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\ActionFilter\Authentication as AuthenticationFilter;
use Bitrix\Main\Engine\ActionFilter\Csrf as CsrfFilter;
use Bitrix\Main\Engine\AutoWire\Parameter;
use Bitrix\Main\Engine\Controller as EngineController;
use Bitrix\Main\Engine\Response\Redirect;
use Bitrix\Main\Engine\Response\Render\Exception\NotFoundPathToViewException;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\SystemException;
use Crasivo\Pages\Domain\Api\PageContentType;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Api\PageRouteFinder;
use Crasivo\Pages\Domain\Api\Query\QueryFilter;
use Crasivo\Pages\Network\Shared\Filter\ModuleRightFilter;

/**
 * @alias \Crasivo\Pages\Controller\Public
 *
 * @final
 * @internal
 */
class PublicController extends EngineController
{
    /**
     * @inheritDoc
     */
    public function configureActions(): array
    {
        return [
            'show' => [
                'class' => __CLASS__,
                '-prefilters' => [
                    CsrfFilter::class,
                ],
                '+prefilters' => [
                    new AuthenticationFilter(),
                    new ModuleRightFilter(ModuleRightFilter::READ),
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAutoWiredParameters(): array
    {
        return [
            new Parameter(
                className: PageRoute::class,
                constructor: function () {
                    $request = $this->getRequest();
                    $pageRoute = ServiceLocator::getInstance()
                        ->get(PageRouteFinder::class)
                        ->getByQueryFilter(new QueryFilter(
                            routeDomain: $request->getHttpHost(),
                            routePath: $request->getRequestedPage(),
                        ));
                    if (!$pageRoute) {
                        throw new NotFoundPathToViewException($request->getRequestedPage());
                    }

                    return $pageRoute;
                },
            ),
        ];
    }

    /**
     * @param PageRoute $pageRoute
     * @return HttpResponse
     */
    public function showAction(
        PageRoute $pageRoute,
    ): HttpResponse
    {
        try {
            return match ($pageRoute->getContentType()) {
                /** @var \Crasivo\Pages\Domain\Api\PageComponent $pageRoute */
                PageContentType::Component => $this->renderComponent(
                    name: $pageRoute->getComponentName(),
                    template: $pageRoute->getComponentTemplate(),
                    params: $pageRoute->getComponentParams(),
                ),
                /** @var \Crasivo\Pages\Domain\Api\PageHtml $pageRoute */
                PageContentType::Html => $this->renderView(
                    viewPath: $pageRoute->getViewPath() ?? 'page/show_html',
                    params: ['pageRoute' => $pageRoute],
                ),
                /** @var \Crasivo\Pages\Domain\Api\PageInclude $pageRoute */
                PageContentType::Include => $this->renderView(
                    viewPath: $pageRoute->getViewPath() ?? 'page/show_include',
                    params: ['pageRoute' => $pageRoute],
                ),
                /** @var \Crasivo\Pages\Domain\Api\PageRedirect $pageRoute */
                PageContentType::Redirect => new Redirect(
                    url: $pageRoute->getRedirectUrl(),
                ),
                default => throw new SystemException('Unsupported page type.'),
            };
        } catch (\Throwable $exception) {
            return $this->renderView(
                'page/show_exception',
                [
                    'exception' => $exception,
                    'pageRoute' => $pageRoute,
                ],
            );
        }
    }
}
