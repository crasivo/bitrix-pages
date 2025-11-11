<?php

namespace Crasivo\Pages\Integration\Database\ORM;

use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\EnumField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\ScalarField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\Contract\Arrayable;
use Bitrix\Main\Type\DateTime;
use Crasivo\Pages\Domain\Api\PageContentType;
use Crasivo\Pages\Domain\Api\PageRoute;
use Crasivo\Pages\Domain\Model\PageRouteModel;
use Crasivo\Pages\Integration\Database\Model\PageComponentModel;
use Crasivo\Pages\Integration\Database\Model\PageHtmlModel;
use Crasivo\Pages\Integration\Database\Model\PageIncludeModel;
use Crasivo\Pages\Integration\Database\Model\PageRedirectModel;

/**
 * @final
 * @internal
 */
class PageRouteTable extends BaseTable
{
    /** @var string */
    public const COLUMN_CONTENT_TYPE = 'CONTENT_TYPE';

    /** @var string */
    public const COLUMN_COMPONENT_NAME = 'COMPONENT_NAME';

    /** @var string */
    public const COLUMN_COMPONENT_PARAMS = 'COMPONENT_PARAMS';

    /** @var string */
    public const COLUMN_COMPONENT_TEMPLATE = 'COMPONENT_TEMPLATE';

    /** @var string */
    public const COLUMN_HTML_CONTENT = 'HTML_CONTENT';

    /** @var string */
    public const COLUMN_INCLUDE_PATH = 'INCLUDE_PATH';

    /** @var string */
    public const COLUMN_REDIRECT_URL = 'REDIRECT_URL';

    /** @var string */
    public const COLUMN_ROUTE_DOMAIN = 'ROUTE_DOMAIN';

    /** @var string */
    public const COLUMN_ROUTE_PATH = 'ROUTE_PATH';

    /**
     * @param PageRoute $model
     * @return mixed|PageRouteModel
     * @throws \Throwable
     */
    public static function createModel(PageRoute $model)
    {
        /** @var PageRoute|Arrayable $model */
        $result = static::add($model->toArray());
        if (!$result->isSuccess()) {
            throw new SystemException(
                sprintf(
                    'Failed to create model %s: %s',
                    get_class($model),
                    implode(', ', $result->getErrorMessages())
                ),
            );
        }

        $model?->setId($result->getId());

        return $result;
    }

    /**
     * @param PageRoute $model
     * @return mixed|void
     * @throws \Throwable
     */
    public static function deleteModel(PageRoute $model)
    {
        /** @var PageRoute|Arrayable $model */
        $result = static::delete($model->getId());
        if (!$result->isSuccess()) {
            throw new SystemException(
                sprintf(
                    'Failed to delete model %s: %s',
                    get_class($model),
                    implode(', ', $result->getErrorMessages())
                ),
            );
        }

        return $result;
    }

    /**
     * @param PageRoute $model
     * @return mixed|void
     * @throws \Throwable
     */
    public static function updateModel(PageRoute $model)
    {
        /** @var PageRoute|Arrayable $model */
        $result = static::update($model->getId(), $model->toArray());
        if (!$result->isSuccess()) {
            throw new SystemException(
                sprintf(
                    'Failed to update model %s: %s',
                    get_class($model),
                    implode(', ', $result->getErrorMessages())
                ),
            );
        }

        return $result;
    }

    /**
     * Returns the DB table schema.
     *
     * @return ScalarField[]
     * @throws \Throwable
     */
    public static function getMap(): array
    {
        return [
            self::COLUMN_ID => new IntegerField(self::COLUMN_ID, [
                'autocomplete' => true,
                'primary' => true,
                'title' => 'ID',
            ]),
            self::COLUMN_ACTIVE => new BooleanField(self::COLUMN_ACTIVE, [
                'default_value' => false,
                'nullable' => false,
                'values' => ['N', 'Y'],
                'required' => true,
                'title' => 'Активность',
            ]),
            self::COLUMN_CONTENT_TYPE => new EnumField(self::COLUMN_CONTENT_TYPE, [
                'nullable' => false,
                'required' => true,
                'values' => PageContentType::getValues(),
                'title' => 'Тип контента',
            ]),
            self::COLUMN_COMPONENT_NAME => new StringField(self::COLUMN_COMPONENT_NAME, [
                'nullable' => true,
                'required' => false,
                'title' => 'Наименование компонента',
            ]),
            self::COLUMN_COMPONENT_PARAMS => new TextField(self::COLUMN_COMPONENT_PARAMS, [
                'nullable' => true,
                'required' => false,
                'serialized' => true,
                'title' => 'Параметры компонента',
            ]),
            self::COLUMN_COMPONENT_TEMPLATE => new StringField(self::COLUMN_COMPONENT_TEMPLATE, [
                'nullable' => true,
                'required' => false,
                'title' => 'Шаблон компонента',
            ]),
            self::COLUMN_HTML_CONTENT => new TextField(self::COLUMN_HTML_CONTENT, [
                'nullable' => true,
                'required' => false,
                'title' => 'HTML контент',
            ]),
            self::COLUMN_INCLUDE_PATH => new StringField(self::COLUMN_INCLUDE_PATH, [
                'nullable' => true,
                'required' => false,
                'title' => 'Путь до файла',
            ]),
            self::COLUMN_REDIRECT_URL => new StringField(self::COLUMN_REDIRECT_URL, [
                'nullable' => true,
                'required' => false,
                'title' => 'URL перенаправления',
            ]),
            self::COLUMN_ROUTE_DOMAIN => new StringField(self::COLUMN_ROUTE_DOMAIN, [
                'nullable' => true,
                'required' => false,
                'title' => 'Доменное имя (хост)',
            ]),
            self::COLUMN_ROUTE_PATH => new StringField(self::COLUMN_ROUTE_PATH, [
                'nullable' => false,
                'required' => true,
                'title' => 'Маршрут',
            ]),
            self::COLUMN_DATE_CREATE => new DatetimeField(self::COLUMN_DATE_CREATE, [
                'default_value' => new DateTime(),
                'nullable' => false,
                'required' => false,
                'title' => 'Дата создания',
            ]),
            self::COLUMN_DATE_MODIFY => new DatetimeField(self::COLUMN_DATE_MODIFY, [
                'default_value' => new DateTime(),
                'nullable' => true,
                'required' => false,
                'title' => 'Дата модификации',
            ]),
        ];
    }

    /**
     * Returns the DB table name.
     *
     * @see <module_dir>/include.php
     * @return string
     */
    public static function getTableName(): string
    {
        return constant('CRASIVO_PAGES_TABLE_PREFIX') . 'pages_route';
    }

    /**
     * @inheritDoc
     */
    public static function isCacheable(): bool
    {
        return true;
    }

    /**
     * @param array $row
     * @return PageRouteModel
     * @throws \Throwable
     */
    public static function convertRowToModel(array $row): PageRouteModel
    {
        $contentType = $row[self::COLUMN_CONTENT_TYPE] ?? 'unknown';

        return match ($contentType) {
            PageContentType::Component->value => PageComponentModel::fromOrmData($row),
            PageContentType::Html->value => PageHtmlModel::fromOrmData($row),
            PageContentType::Include->value => PageIncludeModel::fromOrmData($row),
            PageContentType::Redirect->value => PageRedirectModel::fromOrmData($row),
            default => throw new \Exception(sprintf('Unknown content type: %s', $contentType)),
        };
    }
}
