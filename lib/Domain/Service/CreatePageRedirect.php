<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Service;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Validation\ValidationService;
use Crasivo\Pages\Domain\Api\PageRedirect;
use Crasivo\Pages\Domain\Api\RawPageRedirect;
use Crasivo\Pages\Domain\Exception\PageRouteValidateException;
use Crasivo\Pages\Domain\Factory\PageRouteFactory;
use Crasivo\Pages\Domain\Repository\PageRouteRepository;

/**
 * @final
 * @internal
 */
class CreatePageRedirect implements \Crasivo\Pages\Domain\Api\CreatePageRedirect
{
    /** @var \Bitrix\Main\Diag\Logger|\Psr\Log\LoggerInterface */
    private $logger;

    /** @var ValidationService */
    private ValidationService $mainValidator;

    /** @var PageRouteFactory */
    private PageRouteFactory $pageRouteFactory;

    /** @var PageRouteRepository */
    private PageRouteRepository $pageRouteRepository;

    /**
     * Service constructor.
     *
     * @throws \Throwable
     */
    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $this->logger = $serviceLocator->get('crasivo.pages.logger');
        $this->mainValidator = $serviceLocator->get('main.validation.service');
        $this->pageRouteFactory = $serviceLocator->get(PageRouteFactory::class);
        $this->pageRouteRepository = $serviceLocator->get(PageRouteRepository::class);
    }

    /**
     * @inheritDoc
     */
    public function do(
        RawPageRedirect $rawPageRedirect,
    ): PageRedirect
    {
        try {
            // create empty model
            $pageRoute = $this->pageRouteFactory->createRedirect();

            // fill entity fields
            $pageRoute->setRouteDomain($rawPageRedirect->routeDomain);
            $pageRoute->setRoutePath($rawPageRedirect->routePath);
            $pageRoute->setRedirectUrl($rawPageRedirect->redirectUrl);

            // validate before save
            $validationResult = $this->mainValidator->validate($pageRoute);
            if (!$validationResult->isSuccess()) {
                throw new PageRouteValidateException(
                    validationErrors: $validationResult->getErrors(),
                );
            }

            // save via repository
            $this->pageRouteRepository->put($pageRoute);

            // logging
            $this->logger->info(
                'The page (route) has been created successfully.',
                [
                    'rawPageRedirect' => $rawPageRedirect,
                    'service' => __CLASS__,
                ],
            );

            return $pageRoute;
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Error creating a new page (route).',
                [
                    'exception' => $exception,
                    'rawPageRedirect' => $rawPageRedirect,
                    'service' => __CLASS__,
                ],
            );

            throw $exception;
        }
    }
}
