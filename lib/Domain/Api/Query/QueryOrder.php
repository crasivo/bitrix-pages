<?php

namespace Crasivo\Pages\Domain\Api\Query;

enum QueryOrder: string
{
    case Asc = 'asc';
    case Desc = 'desc';
}
