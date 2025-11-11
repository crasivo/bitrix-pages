<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Model;

use Crasivo\Pages\Domain\Api\PageContentType;

class PageHtmlModel extends PageRouteModel implements \Crasivo\Pages\Domain\Api\PageHtml
{
    /** @var string */
    protected string $htmlContent = '';

    /**
     * @inheritDoc
     */
    final public function getContentType(): PageContentType
    {
        return PageContentType::Html;
    }

    /**
     * @inheritDoc
     */
    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    /**
     * @param string $htmlContent
     * @return void
     */
    public function setHtmlContent(string $htmlContent): void
    {
        $this->htmlContent = $htmlContent;
    }
}
