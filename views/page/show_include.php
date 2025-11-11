<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @global \CMain $APPLICATION */
/** @global \Crasivo\Pages\Domain\Api\PageInclude $pageRoute */

$pageTitle = $pageRoute->getPageProperty('title');
if (is_string($pageTitle) && $pageTitle !== '') {
    $APPLICATION->SetTitle($pageTitle);
}

$APPLICATION->IncludeComponent(
    componentName: 'bitrix:main.include',
    componentTemplate: '.default',
    arParams: [
        'AREA_FILE_SHOW' => 'file',
        'PATH' => $pageRoute->getIncludePath(),
    ],
    parentComponent: false,
    arFunctionParams: [
        'HIDE_ICONS' => 'Y',
    ],
);
