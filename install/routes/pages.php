<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

return static function (\Bitrix\Main\Routing\RoutingConfigurator $routes) {
    if (\Bitrix\Main\Loader::includeModule('crasivo.pages')) {
        \Crasivo\Pages\Integration\Routing\RouteContainer::getInstance()
            ->injectConfigurator($routes);
    }
};
