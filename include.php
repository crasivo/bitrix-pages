<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// Module constants
defined('CRASIVO_PAGES_CACHE_TTL') || @define('CRASIVO_PAGES_CACHE_TTL', 86400);
defined('CRASIVO_PAGES_MODULE_DIR') || @define('CRASIVO_PAGES_MODULE_DIR', __DIR__);
defined('CRASIVO_PAGES_MODULE_ID') || @define('CRASIVO_PAGES_MODULE_ID', 'crasivo.pages');
defined('CRASIVO_PAGES_TABLE_PREFIX') || @define('CRASIVO_PAGES_TABLE_PREFIX', 'c_');

// Additional helper functions
file_exists(__DIR__ . '/functions.php') && require_once __DIR__ . '/functions.php';

/**
 * Declaring aliases for the Network layer.
 *
 * NOTES:
 * - битрикс использует пространства имен (namespaces) для поиска классов-контроллеров
 *
 * @link https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=6436
 * @see .settings.php
 */
@class_alias('Crasivo\\Pages\\Network\\Rest\\Controller\\PageRouteController', 'Crasivo\\Pages\\Controller\\Route');
@class_alias('Crasivo\\Pages\\Network\\Web\\Controller\\PublicController', 'Crasivo\\Pages\\Controller\\Public');
