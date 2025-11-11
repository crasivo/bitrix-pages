<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Database\Model;

use Bitrix\Main\Type\Contract\Arrayable;
use Crasivo\Pages\Integration\Database\ORM\PageRouteTable;

/**
 * @final
 * @internal
 */
class PageHtmlModel extends \Crasivo\Pages\Domain\Model\PageHtmlModel implements Arrayable
{
    /**
     * @param array $row
     * @return self
     */
    public static function fromOrmData(array $row): self
    {
        $model = new self();
        $model->setId((int)$row[PageRouteTable::COLUMN_ID]);
        $model->setActive(in_array($row[PageRouteTable::COLUMN_ACTIVE], [true, 'Y']));
        $model->setRouteDomain((string)$row[PageRouteTable::COLUMN_ROUTE_DOMAIN]);
        $model->setRoutePath((string)$row[PageRouteTable::COLUMN_ROUTE_PATH]);
        $model->setHtmlContent((string)$row[PageRouteTable::COLUMN_HTML_CONTENT]);

        return $model;
    }

    /**
     * NOTES:
     * - может использоваться в конвертерах, которые используют метод Arrayable
     *
     * @inheritDoc
     */
    public function toArray(): array
    {
        return self::toOrmData();
    }

    /**
     * NOTES:
     * - должен возвращать только необходимый для данной модели набор данных
     *
     * @return array
     */
    public function toOrmData(): array
    {
        return [
            PageRouteTable::COLUMN_ID => $this->getId(),
            PageRouteTable::COLUMN_ACTIVE => $this->isActive(),
            PageRouteTable::COLUMN_CONTENT_TYPE => $this->getContentType()->value,
            PageRouteTable::COLUMN_ROUTE_DOMAIN => $this->getRouteDomain(),
            PageRouteTable::COLUMN_ROUTE_PATH => $this->getRoutePath(),
            PageRouteTable::COLUMN_HTML_CONTENT => $this->getHtmlContent(),
        ];
    }
}
