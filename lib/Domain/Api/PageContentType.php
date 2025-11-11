<?php

namespace Crasivo\Pages\Domain\Api;

enum PageContentType: string
{
    case Component = 'component';
    case Custom = 'custom';
    case File = 'file';
    case Html = 'html';
    case Include = 'include';
    case Json = 'json';
    case Php = 'php';
    case Redirect = 'redirect';

    /**
     * @example ['component' => 'Component', 'file' => 'File', '...']
     * @return array
     */
    public static function getItems(): array
    {
        $values = self::getValues();

        return array_combine(
            keys: $values,
            values: array_map('ucfirst', $values),
        );
    }

    /**
     * @example ['component', 'file', 'html', '...']
     * @return string[]
     */
    public static function getValues(): array
    {
        return array_map(fn(\UnitEnum $v) => $v->value, self::cases());
    }
}
