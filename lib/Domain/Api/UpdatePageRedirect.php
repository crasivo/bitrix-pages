<?php

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
interface UpdatePageRedirect
{
    /**
     * Execute service action.
     *
     * @param PageRedirect $pageRedirect
     * @param RawPageRedirect $rawPageRedirect
     * @return PageRedirect
     * @throws \Throwable
     */
    public function do(
        PageRedirect $pageRedirect,
        RawPageRedirect $rawPageRedirect,
    ): PageRedirect;
}
