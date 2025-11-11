<?php

declare(strict_types=1);

namespace Crasivo\Pages\Domain\Api\Query;

class QueryParams
{
    public function __construct(
        public readonly ?QueryFilter $filter = null,
        public readonly int $limit = 20,
        public readonly int $offset = 0,
    )
    {
    }
}
