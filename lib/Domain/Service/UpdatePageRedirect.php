<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Service;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Validation\ValidationService;
use Crasivo\Pages\Domain\Api\PageRedirect;
use Crasivo\Pages\Domain\Api\RawPageRedirect;
use Crasivo\Pages\Domain\Exception\PageRouteValidateException;
use Crasivo\Pages\Domain\Model\PageRedirectModel;
use Crasivo\Pages\Domain\Repository\PageRouteRepository;

class UpdatePageRedirect implements \Crasivo\Pages\Domain\Api\UpdatePageRedirect
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
        PageRedirect $pageRedirect,
        RawPageRedirect $rawPageRedirect,
    ): PageRedirect
    {
        try {
            // note: потом можно добавить upcast
            if (!($pageRedirect instanceof PageRedirectModel)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid %s parameter, requires %s',
                        'pageRedirect',
                        PageRedirectModel::class,
                    ),
                );
            }

            // fill entity fields
            $pageRedirect->setActive($rawPageRedirect->isActive);
            $pageRedirect->setRouteDomain($rawPageRedirect->routeDomain);
            $pageRedirect->setRoutePath($rawPageRedirect->routePath);
            $pageRedirect->setRedirectUrl($rawPageRedirect->redirectUrl);

            // validate before save
            $validationResult = $this->mainValidator->validate($pageRedirect);
            if (!$validationResult->isSuccess()) {
                throw new PageRouteValidateException(
                    validationErrors: $validationResult->getErrors(),
                );
            }

            // save via repository
            $this->pageRouteRepository->put($pageRedirect);

            // logging
            $this->logger->info(
                'The page (route) has been updated successfully.',
                [
                    'rawPageRedirect' => $rawPageRedirect,
                    'service' => __CLASS__,
                ],
            );

            return $pageRedirect;
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Error updating an exists page (route).',
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
