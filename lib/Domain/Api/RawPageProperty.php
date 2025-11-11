<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
class RawPageProperty
{
    /**
     * Raw DTO constructor.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct(
        public readonly string $key,
        public readonly string $value,
    )
    {
    }
}
