<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Action;

use Bitrix\Main\Type\Contract\Arrayable;

/**
 * NOTES:
 * - встроенный в action/controller конвертер данных работает рекурсивно (нам это не надо)
 * - можно воспользоваться отдельными response-объектами, но пока в этом нет острой необходимости
 *
 * @final
 * @internal
 * @mixin \Bitrix\Main\Engine\Action
 */
trait MacroConverterJson
{
    /**
     * Конвертация объекта-модели в массив данных формата `camelCase`.
     *
     * NOTES:
     * - модуль масштабируемый, поэтому требуется универсальное и надежное решение для сериализации моделей
     * - нельзя отдавать наружу приватные данные (свойства)
     *
     * @param object $model
     * @return array
     */
    protected function serializeJsonModel(object $model): array
    {
        if ($model instanceof \JsonSerializable) {
            return (array)$model->jsonSerialize();
        }

        $reflect = new \ReflectionClass($model);
        $reflectProps = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $resultData = [];
        foreach ($reflectProps as $prop) {
            $v = $prop->getValue($model);
            if (is_scalar($v) || is_null($v) || is_array($v)) {
                $resultData[$prop->getName()] = $v;
                continue;
            }
            if ($v instanceof \BackedEnum) {
                $resultData[$prop->getName()] = $v->value;
                continue;
            }
            if ($v instanceof Arrayable) {
                $resultData[$prop->getName()] = $v->toArray();
            }
        }

        return $resultData;
    }
}
