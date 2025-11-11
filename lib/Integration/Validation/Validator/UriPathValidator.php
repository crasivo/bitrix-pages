<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Validation\Validator;

use Bitrix\Main\Validation\ValidationError;
use Bitrix\Main\Validation\ValidationResult;

class UriPathValidator
{
    /**
     * @param mixed $value
     * @return ValidationResult
     */
    public function validate(mixed $value): ValidationResult
    {
        $result = new ValidationResult();
        if (is_null($value)) {
            return $result;
        }
        if (!is_string($value) || false === preg_match('/\/[a-zA-Z0-9\-\_\/\%]+/', $value)) {
            $result->addError(new ValidationError(
                message: 'Value is not a valid URI path',
            ));
        }

        return $result;
    }
}
