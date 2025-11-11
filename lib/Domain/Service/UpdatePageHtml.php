<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Service;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Validation\ValidationService;
use Crasivo\Pages\Domain\Api\PageHtml;
use Crasivo\Pages\Domain\Api\RawPageHtml;
use Crasivo\Pages\Domain\Exception\PageRouteValidateException;
use Crasivo\Pages\Domain\Model\PageHtmlModel;
use Crasivo\Pages\Domain\Repository\PageRouteRepository;

/**
 * @final
 * @internal
 */
class UpdatePageHtml implements \Crasivo\Pages\Domain\Api\UpdatePageHtml
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
        PageHtml $pageHtml,
        RawPageHtml $rawPageHtml,
    ): PageHtml
    {
        try {
            // note: потом можно добавить upcast
            if (!($pageHtml instanceof PageHtmlModel)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid %s parameter, requires %s',
                        'pageHtml',
                        PageHtmlModel::class,
                    ),
                );
            }

            // fill entity fields
            $pageHtml->setActive($rawPageHtml->isActive);
            $pageHtml->setRouteDomain($rawPageHtml->routeDomain);
            $pageHtml->setRoutePath($rawPageHtml->routePath);
            $pageHtml->setHtmlContent($rawPageHtml->htmlContent);

            // validate before save
            $validationResult = $this->mainValidator->validate($pageHtml);
            if (!$validationResult->isSuccess()) {
                throw new PageRouteValidateException(
                    validationErrors: $validationResult->getErrors(),
                );
            }

            // save via repository
            $this->pageRouteRepository->put($pageHtml);

            // logging
            $this->logger->info(
                'The page (route) has been updated successfully.',
                [
                    'rawPageHtml' => $rawPageHtml,
                    'service' => __CLASS__,
                ],
            );

            return $pageHtml;
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Error updating an exists page (route).',
                [
                    'exception' => $exception,
                    'rawPageHtml' => $rawPageHtml,
                    'service' => __CLASS__,
                ],
            );

            throw $exception;
        }
    }
}