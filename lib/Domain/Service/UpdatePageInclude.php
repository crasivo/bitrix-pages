<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Service;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Validation\ValidationService;
use Crasivo\Pages\Domain\Api\PageInclude;
use Crasivo\Pages\Domain\Api\RawPageInclude;
use Crasivo\Pages\Domain\Exception\PageRouteValidateException;
use Crasivo\Pages\Domain\Model\PageIncludeModel;
use Crasivo\Pages\Domain\Repository\PageRouteRepository;

/**
 * @final
 * @internal
 */
class UpdatePageInclude implements \Crasivo\Pages\Domain\Api\UpdatePageInclude
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
        PageInclude $pageInclude,
        RawPageInclude $rawPageInclude,
    ): PageInclude
    {
        try {
            // note: потом можно добавить upcast
            if (!($pageInclude instanceof PageIncludeModel)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid %s parameter, requires %s',
                        'pageInclude',
                        PageIncludeModel::class,
                    ),
                );
            }

            // fill entity fields
            $pageInclude->setActive($rawPageInclude->isActive);
            $pageInclude->setRouteDomain($rawPageInclude->routeDomain);
            $pageInclude->setRoutePath($rawPageInclude->routePath);
            $pageInclude->setIncludePath($rawPageInclude->includePath);

            // validate before save
            $validationResult = $this->mainValidator->validate($pageInclude);
            if (!$validationResult->isSuccess()) {
                throw new PageRouteValidateException(
                    validationErrors: $validationResult->getErrors(),
                );
            }

            // save via repository
            $this->pageRouteRepository->put($pageInclude);

            // logging
            $this->logger->info(
                'The page (route) has been updated successfully.',
                [
                    'rawPageInclude' => $rawPageInclude,
                    'service' => __CLASS__,
                ],
            );

            return $pageInclude;
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Error updating an exists page (route).',
                [
                    'exception' => $exception,
                    'rawPageInclude' => $rawPageInclude,
                    'service' => __CLASS__,
                ],
            );

            throw $exception;
        }
    }
}
