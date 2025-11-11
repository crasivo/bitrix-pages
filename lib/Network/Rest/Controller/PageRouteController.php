<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Rest\Controller;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\AutoWire\Parameter;
use Bitrix\Main\SystemException;
use Bitrix\Main\Validation\Engine\AutoWire\ValidationParameter;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Api\PageRouteFinder;
use Crasivo\Pages\Network\Rest\Action\PageComponentCreateAction;
use Crasivo\Pages\Network\Rest\Action\PageComponentUpdateAction;
use Crasivo\Pages\Network\Rest\Action\PageHtmlCreateAction;
use Crasivo\Pages\Network\Rest\Action\PageHtmlUpdateAction;
use Crasivo\Pages\Network\Rest\Action\PageIncludeCreateAction;
use Crasivo\Pages\Network\Rest\Action\PageIncludeUpdateAction;
use Crasivo\Pages\Network\Rest\Action\PageRedirectUpdateAction;
use Crasivo\Pages\Network\Rest\Action\PageRouteCreateAction;
use Crasivo\Pages\Network\Rest\Action\PageRouteDeleteAction;
use Crasivo\Pages\Network\Rest\Action\PageRouteGetAction;
use Crasivo\Pages\Network\Shared\Request\PageComponentCreateRequest;
use Crasivo\Pages\Network\Shared\Request\PageComponentUpdateRequest;
use Crasivo\Pages\Network\Shared\Request\PageHtmlCreateRequest;
use Crasivo\Pages\Network\Shared\Request\PageHtmlUpdateRequest;
use Crasivo\Pages\Network\Shared\Request\PageRedirectCreateRequest;
use Crasivo\Pages\Network\Shared\Request\PageRedirectUpdateRequest;
use Crasivo\Pages\Network\Shared\Request\PageRouteDeleteRequest;
use Crasivo\Pages\Network\Shared\Request\PageRouteGetRequest;

/**
 * @alias Crasivo\Pages\Controller\Route
 * @final
 * @internal
 * @restUrl /rest/crasivo:pages.route.{action}
 */
class PageRouteController extends \Crasivo\Pages\Network\Shared\Controller\TransactionalController
{
    /**
     * @inheritDoc
     */
    public function configureActions(): array
    {
        return [
            'createComponent' => PageComponentCreateAction::getSettings(),
            'createHtml' => PageHtmlCreateAction::getSettings(),
            'createInclude' => PageIncludeCreateAction::getSettings(),
            'createRedirect' => PageRouteCreateAction::getSettings(),
            'delete' => PageRouteDeleteAction::getSettings(),
            'get' => PageRouteGetAction::getSettings(),
            'updateComponent' => PageComponentUpdateAction::getSettings(),
            'updateHtml' => PageHtmlUpdateAction::getSettings(),
            'updateInclude' => PageIncludeUpdateAction::getSettings(),
            'updateRedirect' => PageRedirectUpdateAction::getSettings(),
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
                    $jsonData = $this->getRequest()->getJsonList();
                    $routePath = $jsonData->get('routePath')
                        ?? throw new \InvalidArgumentException('Invalid route path.');
                    $pageRoute = ServiceLocator::getInstance()
                        ->get(PageRouteFinder::class)
                        ->getByRoutePath($routePath);
                    if (!$pageRoute) {
                        throw new SystemException('Route not found.');
                    }

                    return $pageRoute;
                },
            ),
            new ValidationParameter(
                PageComponentCreateRequest::class,
                fn() => PageComponentCreateRequest::fromRequest($this->request),
            ),
            new ValidationParameter(
                PageComponentUpdateRequest::class,
                fn() => PageComponentUpdateRequest::fromRequest($this->request),
            ),
            new ValidationParameter(
                PageHtmlCreateRequest::class,
                fn() => PageHtmlCreateRequest::fromRequest($this->request),
            ),
            new ValidationParameter(
                PageHtmlUpdateRequest::class,
                fn() => PageHtmlUpdateRequest::fromRequest($this->request),
            ),
            new ValidationParameter(
                PageRedirectCreateRequest::class,
                fn() => PageRedirectCreateRequest::fromRequest($this->request),
            ),
            new ValidationParameter(
                PageRouteDeleteRequest::class,
                fn() => PageRouteDeleteRequest::fromRequest($this->request),
            ),
            new ValidationParameter(
                PageRouteGetRequest::class,
                fn() => PageRouteGetRequest::fromRequest($this->request),
            ),
            new ValidationParameter(
                PageRedirectUpdateRequest::class,
                fn() => PageRedirectUpdateRequest::fromRequest($this->request),
            ),
        ];
    }
}
