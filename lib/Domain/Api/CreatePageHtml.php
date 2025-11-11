<?php

namespace Crasivo\Pages\Domain\Api;

interface CreatePageHtml
{
    /**
     * Execute service action.
     *
     * @param RawPageHtml $rawPageHtml
     * @return PageHtml
     * @throws \Throwable
     */
    public function do(
        RawPageHtml $rawPageHtml,
    ): PageHtml;
}
