<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @global \CMain $APPLICATION */
/** @var \Throwable $exception */

Bitrix\Main\UI\Extension::load([
    'ui.alerts',
]);

$APPLICATION->SetTitle('Error!');

?>
<div class="crasivo-pages__exception">
    <div class="ui-alert ui-alert-danger">
        <span class="ui-alert-message"><strong>Error: </strong> <?= $exception?->getMessage() ?></span>
    </div>
    <div class="ui-alert">
        <code><?= var_export($exception?->getTraceAsString(), true) ?></code>
    </div>
</div>