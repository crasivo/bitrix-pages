<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Database\Factory;

use Crasivo\Pages\Integration\Database\Model\PageComponentModel;
use Crasivo\Pages\Integration\Database\Model\PageHtmlModel;
use Crasivo\Pages\Integration\Database\Model\PageIncludeModel;
use Crasivo\Pages\Integration\Database\Model\PageRedirectModel;

/**
 * @final
 * @internal
 */
class PageRouteFactory implements \Crasivo\Pages\Domain\Factory\PageRouteFactory
{
    /**
     * @inheritDoc
     */
    public function createComponent(): PageComponentModel
    {
        return new PageComponentModel();
    }

    /**
     * @inheritDoc
     */
    public function createHtml(): PageHtmlModel
    {
        return new PageHtmlModel();
    }

    /**
     * @inheritDoc
     */
    public function createInclude(): PageIncludeModel
    {
        return new PageIncludeModel();
    }

    /**
     * @inheritDoc
     */
    public function createRedirect(): PageRedirectModel
    {
        return new PageRedirectModel();
    }
}