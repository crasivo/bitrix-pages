<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @global \CMain $APPLICATION */
/** @global \Crasivo\Pages\Domain\Api\PageHtml $pageRoute */

$pageTitle = $pageRoute->getPageProperty('title');
if (is_string($pageTitle) && $pageTitle !== '') {
    $APPLICATION->SetTitle($pageTitle);
}
?>
<div class="crasivo-pages__html"><?= $pageRoute->getHtmlContent() ?></div>
