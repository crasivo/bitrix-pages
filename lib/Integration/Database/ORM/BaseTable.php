<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Database\ORM;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\ScalarField;
use Bitrix\Main\Type\DateTime;

abstract class BaseTable extends DataManager
{
    /** @var string */
    public const COLUMN_ID = 'ID';

    /** @var string */
    public const COLUMN_ACTIVE = 'ACTIVE';

    /** @var string */
    public const COLUMN_DATE_CREATE = 'DATE_CREATE';

    /** @var string */
    public const COLUMN_DATE_MODIFY = 'DATE_MODIFY';

    /**
     * @return ScalarField[]
     * @throws \Throwable
     */
    public static function getMap(): array
    {
        return [
            self::COLUMN_ID => new IntegerField(self::COLUMN_ID, [
                'autocomplete' => true,
                'primary' => true,
            ]),
            self::COLUMN_ACTIVE => new BooleanField(self::COLUMN_ACTIVE, [
                'default_value' => false,
                'nullable' => false,
                'required' => true,
                'values' => ['N', 'Y'],
            ]),
            self::COLUMN_DATE_CREATE => new DatetimeField(self::COLUMN_DATE_CREATE, [
                'default_value' => new DateTime(),
                'nullable' => false,
                'required' => false,
            ]),
            self::COLUMN_DATE_MODIFY => new DatetimeField(self::COLUMN_DATE_MODIFY, [
                'default_value' => new DateTime(),
                'nullable' => true,
                'required' => false,
            ]),
        ];
    }
}