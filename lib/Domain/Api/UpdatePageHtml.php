<?php

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
interface UpdatePageHtml
{
    /**
     * Execute service action.
     *
     * @param PageHtml $pageHtml
     * @param RawPageHtml $rawPageHtml
     * @return PageHtml
     * @throws \Throwable
     */
    public function do(
        PageHtml $pageHtml,
        RawPageHtml $rawPageHtml,
    ): PageHtml;
}
