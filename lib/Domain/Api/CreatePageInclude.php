<?php

namespace Crasivo\Pages\Domain\Api;

interface CreatePageInclude
{
    /**
     * Execute service action.
     *
     * @param RawPageInclude $rawPageInclude
     * @return PageInclude
     * @throws \Throwable
     */
    public function do(
        RawPageInclude $rawPageInclude,
    ): PageInclude;
}