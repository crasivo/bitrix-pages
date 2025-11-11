<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if (class_exists('\crasivo_pages')) return;

use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Security\Random;

IncludeModuleLangFile(__FILE__);

class crasivo_pages extends \CModule
{
    /** @var string */
    public const MAIN_MIN_VERSION = '25.700.0';

    /** @var string  */
    public const PHP_MIN_VERSION = '8.1';

    /**
     * Installer constructor
     *
     * @throws \Throwable
     */
    public function __construct()
    {
        // define general vars
        $this->MODULE_ID = 'crasivo.pages';
        $this->MODULE_NAME = GetMessage('CRASIVO_PAGES_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('CRASIVO_PAGES_MODULE_DESC');
        $this->MODULE_VERSION = '0.0.1';
        $this->MODULE_VERSION_DATE = '2025-11-06 12:00:00';
        $this->PARTNER_NAME = 'Crasivo';
        $this->PARTNER_URI = 'https://github.com/crasivo';

        // define module version
        if (is_file(__DIR__ . '/version.php')) {
            include(__DIR__ . '/version.php');
        }
        if (isset($arModuleVersion) && is_array($arModuleVersion)) {
            if (isset($arModuleVersion['VERSION']) && $arModuleVersion['VERSION'] <> '') {
                $this->MODULE_VERSION = (string)$arModuleVersion['VERSION'];
            }
            if (isset($arModuleVersion['VERSION_DATE']) && $arModuleVersion['VERSION_DATE'] <> '') {
                $this->MODULE_VERSION_DATE = (string)$arModuleVersion['VERSION_DATE'];
            }
        }
    }

    /**
     * Module installation process.
     *
     * @return mixed|void
     * @throws \Throwable
     */
    public function DoInstall()
    {
        try {
            $this->checkModuleRequirements();
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();
        } catch (\Throwable $exception) {
            \UnRegisterModule($this->MODULE_ID);
            $GLOBALS['APPLICATION']->ThrowException($exception);
        }
    }

    /**
     * The module's uninstall process
     *
     * @return mixed|void
     * @throws \Throwable
     */
    public function DoUninstall()
    {
        try {
            $this->UnInstallFiles();
            $this->UnInstallEvents();
            $this->UnInstallDB();
        } catch (\Throwable $exception) {
            $GLOBALS['APPLICATION']->ThrowException($exception);
        }
    }

    /**
     * Changing the database structure.
     *
     * @return mixed|void
     * @throws \Throwable
     */
    public function InstallDB()
    {
        try {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->installDatabaseSql();
            $this->installModuleOptions();
        } catch (\Throwable $exception) {
            ModuleManager::unRegisterModule($this->MODULE_ID);

            throw $exception;
        }
    }

    /**
     * Registration of event handlers.
     *
     * @return mixed|void
     * @throws \Throwable
     */
    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, \Crasivo\Pages\Integration\Admin\Event\OnBuildGlobalMenu::class, 'do', 10);
        $eventManager->registerEventHandler('rest', 'OnRestCheckAuth', $this->MODULE_ID, \Crasivo\Pages\Integration\Rest\Event\OnRestCheckAuth::class, 'do', 10);
    }

    /**
     * Changing the file structure.
     *
     * @return mixed|void
     * @throws \Throwable
     */
    public function InstallFiles()
    {
        \CopyDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
        @mkdir($_SERVER['DOCUMENT_ROOT'] . '/local/components/' . $this->MODULE_ID, 0755, true);
        \CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components/' . $this->MODULE_ID, true, true);
        @mkdir($_SERVER['DOCUMENT_ROOT'] . '/local/routes', 0755, true);
        \CopyDirFiles(__DIR__ . '/routes', $_SERVER['DOCUMENT_ROOT'] . '/local/routes', true);
        $this->installRoutingConfiguration();
    }

    /**
     * Rollback of changes in the database structure.
     *
     * @return mixed|void
     * @throws \Throwable
     */
    public function UnInstallDB()
    {
        $this->uninstallModuleOptions();
        $this->uninstallDatabaseSql();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler('menu', 'OnBuildGlobalMenu', $this->MODULE_ID, \Crasivo\Pages\Integration\Admin\Event\OnBuildGlobalMenu::class, 'do', 10);
        $eventManager->unRegisterEventHandler('rest', 'OnRestCheckAuth', $this->MODULE_ID, \Crasivo\Pages\Integration\Rest\Event\OnRestCheckAuth::class, 'do', 10);
    }

    /**
     * Rollback of changes in the file structure.
     *
     * @return mixed|void
     * @throws \Throwable
     */
    public function UnInstallFiles()
    {
        \DeleteDirFiles(__DIR__ . '/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
        \DeleteDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components/' . $this->MODULE_ID);
        \DeleteDirFiles(__DIR__ . '/routes', $_SERVER['DOCUMENT_ROOT'] . '/local/routes');
        $this->uninstallRoutingConfiguration();
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function checkModuleRequirements()
    {
        if (version_compare(PHP_VERSION, self::PHP_MIN_VERSION, '<')) {
            throw new \Exception(\GetMessage('CRASIVO_PAGES_ERROR_PHP_VERSION', [
                '#PHP_MIN_VERSION#' => self::PHP_MIN_VERSION,
            ]));
        }
        if (version_compare(SM_VERSION, self::MAIN_MIN_VERSION, '<')) {
            throw new \Exception(\GetMessage('CRASIVO_PAGES_ERROR_MAIN_VERSION', [
                '#MAIN_MIN_VERSION#' => self::MAIN_MIN_VERSION,
            ]));
        }
        if (!ModuleManager::isModuleInstalled('rest')) {
            throw new \Exception(\GetMessage('CRASIVO_PAGES_ERROR_MODULE_INSTALL', [
                '#MODULE_ID#' => 'rest',
            ]));
        }
    }

    /**
     * @return void
     * @throws \Throwable
     */
    private function installDatabaseSql()
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $dbDir = __DIR__ . '/database/' . $connection->getType();
        if (!is_dir($dbDir)) {
            throw new \Exception('Unsupported connection type');
        }

        foreach (glob($dbDir . '/install_*.sql') as $sqlFile) {
            $connection->executeSqlBatch(file_get_contents($sqlFile));
        }
    }

    /**
     * @return void
     * @throws \Throwable
     */
    private function installModuleOptions()
    {
        // logging
        Option::set($this->MODULE_ID, 'log_level', 'error');
        Option::set($this->MODULE_ID, 'log_file_enabled', 'N');
        Option::set($this->MODULE_ID, 'log_file_dir', '%upload_dir%/logs/' . $this->MODULE_ID);
        // rest
        Option::set($this->MODULE_ID, 'rest_auth_enabled', 'N');
        Option::set($this->MODULE_ID, 'rest_auth_token', Random::getString(32));
        Option::set($this->MODULE_ID, 'rest_auth_user', (int)$GLOBALS['USER']->GetId());
    }

    /**
     * @return void
     * @throws \Throwable
     */
    private function installRoutingConfiguration(): void
    {
        $kernelConfig = \Bitrix\Main\Config\Configuration::getInstance();
        $routingValue = $kernelConfig->get('routing');
        if (!isset($routingValue['config']) || !is_array($routingValue['config'])) {
            $routingValue['config'] = [];
        }

        $routingValue['config'][] = 'pages.php';
        $kernelConfig->add('routing', $routingValue);
        $kernelConfig->saveConfiguration();
    }

    /**
     * @return void
     * @throws \Throwable
     */
    private function uninstallDatabaseSql()
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $dbDir = __DIR__ . '/database/' . $connection->getType();
        if (!is_dir($dbDir)) {
            throw new \Exception('Unsupported connection type');
        }

        foreach (glob($dbDir . '/uninstall*.sql') as $sqlFile) {
            $connection->executeSqlBatch(file_get_contents($sqlFile));
        }
    }

    /**
     * @return void
     * @throws \Throwable
     */
    private function uninstallModuleOptions()
    {
        Option::delete($this->MODULE_ID);
    }

    /**
     * @return void
     * @throws \Throwable
     */
    private function uninstallRoutingConfiguration(): void
    {
        $kernelConfig = \Bitrix\Main\Config\Configuration::getInstance();
        $routingValue = $kernelConfig->get('routing');
        if (!isset($routingValue['config']) || !is_array($routingValue['config'])) {
            $routingValue['config'] = [];
        }

        $key = array_search('pages.php', $routingValue['config']);
        if (false === $key) {
            return;
        }

        unset($routingValue['config'][$key]);
        $kernelConfig->add('routing', $routingValue);
        $kernelConfig->saveConfiguration();
    }
}
