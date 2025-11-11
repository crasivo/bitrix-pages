<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Validation\Validator;

use Bitrix\Main\Validation\ValidationError;
use Bitrix\Main\Validation\ValidationResult;
use Bitrix\Main\Validation\Validator\ValidatorInterface;

class DomainValidator implements ValidatorInterface
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
        if (!is_string($value) || false === filter_var($value, \FILTER_VALIDATE_DOMAIN)) {
            $result->addError(new ValidationError(
                message: 'Value is not a valid domain.',
            ));
        }

        return $result;
    }
}