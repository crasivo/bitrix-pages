<?php

namespace Crasivo\Pages\Domain\Factory;

use Crasivo\Pages\Domain\Model\PageComponentModel;
use Crasivo\Pages\Domain\Model\PageHtmlModel;
use Crasivo\Pages\Domain\Model\PageIncludeModel;
use Crasivo\Pages\Domain\Model\PageRedirectModel;

/**
 * @final
 * @public
 */
interface PageRouteFactory
{
    /**
     * @return PageComponentModel
     * @throws \Throwable
     */
    public function createComponent(): PageComponentModel;

    /**
     * @return PageHtmlModel
     * @throws \Throwable
     */
    public function createHtml(): PageHtmlModel;

    /**
     * @return PageIncludeModel
     * @throws \Throwable
     */
    public function createInclude(): PageIncludeModel;

    /**
     * @return PageRedirectModel
     * @throws \Throwable
     */
    public function createRedirect(): PageRedirectModel;
}
