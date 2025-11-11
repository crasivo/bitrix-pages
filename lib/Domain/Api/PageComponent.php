<?php

namespace Crasivo\Pages\Domain\Api;

interface PageComponent extends PageRoute
{
    /**
     * Возвращает наименование компонента.
     *
     * @example bitrix:main.include
     * @return string
     */
    public function getComponentName(): string;

    /**
     * Возвращает полный список параметров компонента.
     *
     * @return array|null
     */
    public function getComponentParams(): ?array;

    /**
     * Возвращает наименование шаблона для
     *
     * @return string|null
     */
    public function getComponentTemplate(): ?string;
}
