<?php

namespace Crasivo\Pages\Domain\Api;

/**
 * @final
 * @public
 */
interface PageInclude extends PageRoute
{
    /**
     * Возвращает абсолютный путь к файлу включаемой области.
     *
     * @example %document_root%/include/note.php
     * @return string
     */
    public function getIncludePath(): string;
}
