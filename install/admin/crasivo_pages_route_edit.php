<?php
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/modules/crasivo.pages/admin/crasivo_pages_route_edit.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/local/modules/crasivo.pages/admin/crasivo_pages_route_edit.php';
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/crasivo.pages/admin/crasivo_pages_route_edit.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/crasivo.pages/admin/crasivo_pages_route_edit.php';
}
