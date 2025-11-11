<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Validation\Rule;

use Attribute;
use Bitrix\Main\Validation\Rule\AbstractPropertyValidationAttribute;
use Crasivo\Pages\Integration\Validation\Validator\ComponentTemplateValidator;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class ComponentTemplate extends AbstractPropertyValidationAttribute
{
    protected function getValidators(): array
    {
        return [
            new ComponentTemplateValidator(),
        ];
    }
}