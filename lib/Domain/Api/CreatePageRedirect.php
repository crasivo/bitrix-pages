<?php

namespace Crasivo\Pages\Domain\Api;

interface CreatePageRedirect
{
    /**
     * Execute service action.
     *
     * @param RawPageRedirect $rawPageRedirect
     * @return PageRedirect
     * @throws \Throwable
     */
    public function do(
        RawPageRedirect $rawPageRedirect,
    ): PageRedirect;
}
