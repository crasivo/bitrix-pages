<?php

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
interface UpdatePageInclude
{
    /**
     * Execute service action.
     *
     * @param PageInclude $pageInclude
     * @param RawPageInclude $rawPageInclude
     * @return PageInclude
     * @throws \Throwable
     */
    public function do(
        PageInclude $pageInclude,
        RawPageInclude $rawPageInclude,
    ): PageInclude;
}
