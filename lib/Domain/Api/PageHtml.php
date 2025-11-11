<?php

namespace Crasivo\Pages\Domain\Api;

interface PageHtml extends PageRoute
{
    /**
     * Возвращает HTML контент для отображения на странице.
     *
     * @return string
     */
    public function getHtmlContent(): string;
}
