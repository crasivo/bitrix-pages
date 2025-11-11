<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Admin\Event;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/local/modules/crasivo.pages/lib.php');

/**
 * Обработчик события: Добавление пункта в меню в панели администратора.
 *
 * @internal
 * @see \CAdminMenu::Init
 */
class OnBuildGlobalMenu
{
    /** @var string */
    public const MODULE_ID = 'crasivo.pages';

    /**
     * Execute event handler.
     *
     * @param array $globalMenu
     * @param array $moduleMenu
     * @return mixed|void
     */
    public static function do(array $globalMenu, array &$moduleMenu)
    {
        /** @global \CMain $APPLICATION */
        /** @global \CUser $USER */
        global $APPLICATION, $USER;

        // check module rights
        $moduleRight = $APPLICATION->GetGroupRight(self::MODULE_ID);
        if ($moduleRight < 'R' && !$USER->IsAdmin()) {
            return null;
        }

        // Root menu (sidebar) > Context menu (module)
        $contextMenu = [
            'parent_menu' => 'global_menu_settings',
            'icon' => 'default_menu_icon',
            'page_icon' => 'default_page_icon',
            'sort' => 500,
            'text' => Loc::getMessage('CRASIVO_PAGES_ADMIN_MODULE_MENU_TEXT'),
            'title' => Loc::getMessage('CRASIVO_PAGES_ADMIN_MODULE_MENU_TITLE'),
            'items' => [],
        ];

        // Root menu (sidebar) > Context menu (module) > Submenu (items)
        $contextMenu['items'][] = [
            'text' => Loc::getMessage('CRASIVO_PAGES_ADMIN_MODULE_SUBMENU_ROUTE_LIST_TEXT'),
            'title' => Loc::getMessage('CRASIVO_PAGES_ADMIN_MODULE_SUBMENU_ROUTE_LIST_TITLE'),
            'url' => '/bitrix/admin/crasivo_pages_route_list.php',
            'more_url' => '/bitrix/admin/crasivo_pages_route_list.php',
        ];

        $moduleMenu[] = $contextMenu;
    }
}
