<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Service;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Validation\ValidationService;
use Crasivo\Pages\Domain\Api\PageComponent;
use Crasivo\Pages\Domain\Api\RawPageComponent;
use Crasivo\Pages\Domain\Exception\PageRouteValidateException;
use Crasivo\Pages\Domain\Model\PageComponentModel;
use Crasivo\Pages\Domain\Repository\PageRouteRepository;

/**
 * @final
 * @internal
 */
class UpdatePageComponent implements \Crasivo\Pages\Domain\Api\UpdatePageComponent
{
    /** @var \Bitrix\Main\Diag\Logger|\Psr\Log\LoggerInterface */
    private $logger;

    /** @var ValidationService */
    private ValidationService $mainValidator;

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
        $this->pageRouteRepository = $serviceLocator->get(PageRouteRepository::class);
    }

    /**
     * @inheritDoc
     */
    public function do(
        PageComponent $pageComponent,
        RawPageComponent $rawPageComponent,
    ): PageComponent
    {
        try {
            // note: потом можно добавить upcast
            if (!($pageComponent instanceof PageComponentModel)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid %s parameter, requires %s',
                        'pageComponent',
                        PageComponentModel::class,
                    ),
                );
            }

            // fill entity fields
            $pageComponent->setActive($rawPageComponent->isActive);
            $pageComponent->setRouteDomain($rawPageComponent->routeDomain);
            $pageComponent->setRoutePath($rawPageComponent->routePath);
            $pageComponent->setComponentName($rawPageComponent->componentName);
            $pageComponent->setComponentParams($rawPageComponent->componentParams);
            $pageComponent->setComponentTemplate($rawPageComponent->componentTemplate);

            // validate before save
            $validationResult = $this->mainValidator->validate($pageComponent);
            if (!$validationResult->isSuccess()) {
                throw new PageRouteValidateException(
                    validationErrors: $validationResult->getErrors(),
                );
            }

            // save via repository
            $this->pageRouteRepository->put($pageComponent);

            // logging
            $this->logger->info(
                'The page (route) has been updated successfully.',
                [
                    'rawPageComponent' => $rawPageComponent,
                    'service' => __CLASS__,
                ],
            );

            return $pageComponent;
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Error updating an exists page (route).',
                [
                    'exception' => $exception,
                    'rawPageComponent' => $rawPageComponent,
                    'service' => __CLASS__,
                ],
            );

            throw $exception;
        }
    }
}
