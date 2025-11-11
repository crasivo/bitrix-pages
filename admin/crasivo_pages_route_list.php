<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php');

/** @global CMain $APPLICATION */

// Check module rights
if ($APPLICATION->GetGroupRight('crasivo.pages') <= 'R') {
    $APPLICATION->AuthForm('');
}

// Check system errors
if ($ex = $APPLICATION->GetException()) {
    ShowError($ex->GetString());
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
    die();
}

// Include component
$APPLICATION->IncludeComponent(
    componentName: defined('CRASIVO_PAGES_ADMIN_ROUTE_LIST_COMPONENT')
        ? constant('CRASIVO_PAGES_ADMIN_ROUTE_LIST_COMPONENT')
        : 'crasivo.pages:admin.route.list',
    componentTemplate: defined('CRASIVO_PAGES_ADMIN_ROUTE_LIST_COMPONENT_TEMPLATE')
        ? constant('CRASIVO_PAGES_ADMIN_ROUTE_LIST_COMPONENT_TEMPLATE')
        : '.default',
    arParams: [
        'ROUTE_LIST_URL' => '/bitrix/admin/crasivo_pages_route_list.php',
        'ROUTE_EDIT_URL' => '/bitrix/admin/crasivo_pages_route_edit.php',
    ],
    parentComponent: false,
    arFunctionParams: [
        'HIDE_ICONS' => 'Y',
    ],
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
