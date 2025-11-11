<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Database\Service;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Validation\ValidationService;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Exception\PageRouteValidateException;
use Crasivo\Pages\Domain\Repository\PageRouteRepository;
use Crasivo\Pages\Integration\Database\ORM\PageRouteTable;

/**
 * @internal
 */
final class SaveOrmPageRoute implements \Crasivo\Pages\Integration\Database\Api\SaveOrmPageRoute
{
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
        $this->mainValidator = $serviceLocator->get('main.validation.service');
        $this->pageRouteRepository = $serviceLocator->get(PageRouteRepository::class);
    }

    /**
     * @inheritDoc
     */
    public function do(array $ormData): PageRoute
    {
        // convert to model
        $pageRoute = PageRouteTable::convertRowToModel($ormData);

        // validate before save
        $validationResult = $this->mainValidator->validate($pageRoute);
        if (!$validationResult->isSuccess()) {
            throw new PageRouteValidateException(
                validationErrors: $validationResult->getErrors(),
            );
        }

        $this->pageRouteRepository->put($pageRoute);

        return $pageRoute;
    }
}
