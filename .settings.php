<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Crasivo\Pages\Domain;
use Crasivo\Pages\Integration;

return [
    'controllers' => [
        'value' => [
            // note: алиасы классов объявлены в include.php
            'defaultNamespace' => 'Crasivo\\Pages\\Controller',
            'restIntegration' => [
                'enabled' => true,
            ],
        ],
        'readonly' => true,
    ],
    'loggers' => [
        'value' => [
            'crasivo.pages' => ['constructor' => fn() => Integration\Diag\DefaultLogger::getInstance()],
        ],
        'readonly' => true,
    ],
    'services' => [
        'value' => [
            'crasivo.pages.logger' => ['constructor' => fn() => Integration\Diag\DefaultLogger::getInstance()],
            // Domain / Api > Domain / Service
            Domain\Api\CreatePageComponent::class => ['className' => Domain\Service\CreatePageComponent::class],
            Domain\Api\CreatePageHtml::class => ['className' => Domain\Service\CreatePageHtml::class],
            Domain\Api\CreatePageInclude::class => ['className' => Domain\Service\CreatePageInclude::class],
            Domain\Api\CreatePageRedirect::class => ['className' => Domain\Service\CreatePageRedirect::class],
            Domain\Api\RemovePageRoute::class => ['className' => Domain\Service\RemovePageRoute::class],
            Domain\Api\UpdatePageComponent::class => ['className' => Domain\Service\UpdatePageComponent::class],
            Domain\Api\UpdatePageHtml::class => ['className' => Domain\Service\UpdatePageHtml::class],
            Domain\Api\UpdatePageInclude::class => ['className' => Domain\Service\UpdatePageInclude::class],
            Domain\Api\UpdatePageRedirect::class => ['className' => Domain\Service\UpdatePageRedirect::class],
            // Domain / Factory > Integration / Factory
            Domain\Factory\PageRouteFactory::class => ['className' => Integration\Database\Factory\PageRouteFactory::class],
            // Domain / Finder > Integration / Repository
            Domain\Api\PageRouteFinder::class => ['constructor' => fn() => Integration\Database\Repository\PageRouteRepository::getInstance()],
            // Domain / Registry > Integration / Registry
            Domain\Registry\PageRouteRegistry::class => ['constructor' => fn() => Integration\Database\Registry\PageRouteRegistry::getInstance()],
            // Domain / Repository > Integration / Repository
            Domain\Repository\PageRouteRepository::class => ['constructor' => fn() => Integration\Database\Repository\PageRouteRepository::getInstance()],
            // Integration / Database (system)
            Integration\Database\Api\SaveOrmPageRoute::class => ['className' => Integration\Database\Service\SaveOrmPageRoute::class],
        ],
        'readonly' => true,
    ],
];
