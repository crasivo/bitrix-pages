<?php

namespace Crasivo\Pages\Domain\Api;

interface CreatePageComponent
{
    /**
     * Execute service action.
     *
     * @param RawPageComponent $rawPageComponent
     * @return PageComponent
     * @throws \Throwable
     */
    public function do(
        RawPageComponent $rawPageComponent,
    ): PageComponent;
}
