<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Validation\Validator;

use Bitrix\Main\Validation\ValidationError;
use Bitrix\Main\Validation\ValidationResult;
use Bitrix\Main\Validation\Validator\ValidatorInterface;

class ScalarKeyValueValidator implements ValidatorInterface
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

        try {
            if (!is_array($value)) {
                throw new \InvalidArgumentException('Value must be an array.');
            }

            foreach ($value as $k => $v) {
                if (!is_scalar($v)) {
                    throw new \InvalidArgumentException(sprintf('Value %s must be scalar, %s given.', $k, gettype($v)));
                }
            }
        } catch (\Throwable $exception) {
            $result->addError(new ValidationError(
                message: $exception->getMessage(),
            ));
        }

        return $result;
    }
}