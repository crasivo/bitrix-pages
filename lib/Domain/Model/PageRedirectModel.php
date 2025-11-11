<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Model;

use Bitrix\Main\Validation\Rule\Url;
use Crasivo\Pages\Domain\Api\PageContentType;

class PageRedirectModel extends PageRouteModel implements \Crasivo\Pages\Domain\Api\PageRedirect
{
    #[Url]
    protected string $redirectUrl;

    /**
     * @inheritDoc
     */
    final public function getContentType(): PageContentType
    {
        return PageContentType::Redirect;
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     * @return void
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }
}
