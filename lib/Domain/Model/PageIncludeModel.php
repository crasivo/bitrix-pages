<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Model;

use Crasivo\Pages\Domain\Api\PageContentType;

class PageIncludeModel extends PageRouteModel implements \Crasivo\Pages\Domain\Api\PageInclude
{
    /** @var string */
    protected string $includePath = '';

    /**
     * @inheritDoc
     */
    final public function getContentType(): PageContentType
    {
        return PageContentType::Include;
    }

    /**
     * @inheritDoc
     */
    public function getIncludePath(): string
    {
        return $this->includePath;
    }

    /**
     * @param string $includePath
     * @return void
     */
    public function setIncludePath(string $includePath): void
    {
        $this->includePath = $includePath;
    }
}
