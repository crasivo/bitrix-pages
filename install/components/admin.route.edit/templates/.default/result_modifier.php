<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

if (empty($arResult['PAGE_ROUTE']['COMPONENT_PARAMS'])) {
    $arResult['PAGE_ROUTE']['COMPONENT_PARAMS'] = [];
}

$arResult['PAGE_ROUTE']['COMPONENT_PARAMS_JSON'] = is_array($arResult['PAGE_ROUTE']['COMPONENT_PARAMS']) || is_object($arResult['PAGE_ROUTE']['COMPONENT_PARAMS'])
    ? \Bitrix\Main\Web\Json::encode($arResult['PAGE_ROUTE']['COMPONENT_PARAMS'])
    : (string)$arResult['PAGE_ROUTE']['COMPONENT_PARAMS'];
