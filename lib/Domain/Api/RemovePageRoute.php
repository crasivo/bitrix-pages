<?php

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
interface RemovePageRoute
{
    /**
     * Execute service action.
     *
     * @param PageRoute $pageRoute
     * @return mixed|void
     * @throws \Throwable
     */
    public function do(
        PageRoute $pageRoute,
    );
}
