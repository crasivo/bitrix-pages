<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Validation\Rule;

use Attribute;
use Bitrix\Main\Validation\Rule\AbstractPropertyValidationAttribute;
use Crasivo\Pages\Integration\Validation\Validator\ScalarKeyValueValidator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class ScalarKeyValue extends AbstractPropertyValidationAttribute
{
    protected function getValidators(): array
    {
        return [
            new ScalarKeyValueValidator(),
        ];
    }
}