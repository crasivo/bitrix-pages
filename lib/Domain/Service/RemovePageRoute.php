<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Service;

use Bitrix\Main\DI\ServiceLocator;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Repository\PageRouteRepository;

/**
 * @final
 * @internal
 */
class RemovePageRoute implements \Crasivo\Pages\Domain\Api\RemovePageRoute
{
    /** @var \Bitrix\Main\Diag\Logger|\Psr\Log\LoggerInterface */
    private $logger;

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
        $this->pageRouteRepository = $serviceLocator->get(PageRouteRepository::class);
    }

    /**
     * @inheritDoc
     */
    public function do(PageRoute $pageRoute): void
    {
        try {
            // remove via repository
            $this->pageRouteRepository->remove($pageRoute);

            // logging
            $this->logger->info(
                'The page (route) has been removed successfully.',
                [
                    'pageRouteId' => $pageRoute->getId(),
                    'service' => __CLASS__,
                ],
            );
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Error deleting a page (route).',
                [
                    'exception' => $exception,
                    'pageRouteId' => $pageRoute->getId(),
                    'service' => __CLASS__,
                ],
            );

            throw $exception;
        }
    }
}
