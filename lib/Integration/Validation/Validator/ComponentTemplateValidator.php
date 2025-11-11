<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Validation\Validator;

use Bitrix\Main\Validation\ValidationError;
use Bitrix\Main\Validation\ValidationResult;
use Bitrix\Main\Validation\Validator\ValidatorInterface;

class ComponentTemplateValidator implements ValidatorInterface
{
    public function validate(mixed $value): ValidationResult
    {
        $result = new ValidationResult();
        if (is_null($value)) {
            return $result;
        }
        if (!is_string($value) || false === preg_match('/^([\w\d\-\_\.]+)$/', $value)) {
            $result->addError(new ValidationError(
                message: 'Value is not a valid component name.',
            ));
        }

        return $result;
    }
}