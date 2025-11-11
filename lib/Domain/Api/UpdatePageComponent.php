<?php

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
interface UpdatePageComponent
{
    /**
     * Execute service action.
     *
     * @param PageComponent $pageComponent
     * @param RawPageComponent $rawPageComponent
     * @return PageComponent
     * @throws \Throwable
     */
    public function do(
        PageComponent $pageComponent,
        RawPageComponent $rawPageComponent,
    ): PageComponent;
}
