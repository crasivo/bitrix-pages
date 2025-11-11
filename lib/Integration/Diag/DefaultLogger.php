<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Diag;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\FileLogger;
use Bitrix\Main\Diag\JsonLinesFormatter;

/**
 * @final
 * @internal
 */
class DefaultLogger extends FileLogger
{
    /** @var string */
    public const MODULE_ID = 'crasivo.pages';

    /** @var DefaultLogger|null */
    private static ?self $instance = null;

    /**
     * @return static
     */
    public static function fromModuleOptions(): self
    {
        // detect directory
        $logDir = Option::get(self::MODULE_ID, 'log_dir');
        if (!is_string($logDir) || $logDir === '') {
            $logDir = '%upload_dir%/logs';
        }
        $logDir = str_replace(
            ['%document_root%', '%upload_dir%'],
            [$_SERVER['DOCUMENT_ROOT'], $_SERVER['DOCUMENT_ROOT'] . '/upload'],
            $logDir,
        );
        if (!is_dir($logDir)) {
            @mkdir($logDir, defined('BX_DIR_PERMISSIONS') ? BX_DIR_PERMISSIONS : 0755, true);
        }

        // detect level
        $logLevel = Option::get(self::MODULE_ID, 'log_level');
        if (!is_string($logLevel) || $logLevel === '') {
            $logLevel = 'error';
        }

        // build logger
        $logger = new self(
            fileName: sprintf($logDir . '/%s.log', self::MODULE_ID),
        );

        $logger->setLevel($logLevel);
        $logger->setFormatter(new JsonLinesFormatter());

        return $logger;
    }

    /**
     * Singleton initializer.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        return self::$instance ??= self::fromModuleOptions();
    }
}
