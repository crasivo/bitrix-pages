<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @global CAdminSidePanelHelper $adminSidePanelHelper */
/** @global CAdminPage $adminPage */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CrasivoPagesAdminRouteEditComponent $component */

use Bitrix\Main\Localization\Loc;
use Crasivo\Pages\Domain\Api\PageContentType;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle($arParams['PAGE_TITLE']);

if (is_string($arResult['ERROR']) && $arResult['ERROR'] !== '') {
    ShowError($arResult['ERROR']);
    return;
}

$contextMenu = new CAdminContextMenu($arResult['CONTEXT_MENU']);
$contextMenu->Show();
$tabControl = new CAdminTabControl('tabControl', $arResult['FORM_TABS']);

?><form enctype="multipart/form-data" method="POST" action="<?= $arResult['FORM_ACTION'] ?>" name="crasivo_route_edit">
    <input type="hidden" name="action" value="save" readonly/>
    <input type="hidden" name="ID" value="<?= $arResult['ROUTE']['ID'] ?>" readonly/>
    <?php $tabControl->Begin(); ?>
    <?php $tabControl->BeginNextTab(); ?>
    <tr>
        <td width="40%">
            <span>ID:</span>
        </td>
        <td width="60%">
            <span><?= ($arResult['PAGE_ROUTE']['ID'] > 0 ? $arResult['PAGE_ROUTE']['ID'] : '-') ?></span>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="adm-required-field"><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_ACTIVE') ?>:</span>
        </td>
        <td width="60%">
            <input type="hidden" name="ACTIVE" value="N">
            <input type="checkbox" name="ACTIVE" value="Y" <?= ($arResult['PAGE_ROUTE']['ACTIVE'] ? ' checked' : ''); ?> required/>
        </td>
    </tr>
    <tr id="tr_BLOCK_ROUTE_ACTIVE">
        <td width="40%">
            <span class="adm-required-field"><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_CONTENT_TYPE') ?>:</span>
        </td>
        <td width="60%">
            <input type="hidden" name="CONTENT_TYPE" value="custom"/>
            <select id="CONTENT_TYPE" style="max-width: 300px; width: 300px;" name="CONTENT_TYPE" required>
                <?php foreach (PageContentType::getItems() as $k => $v): ?> ?>
                <option value="<?= $k ?>" <?= ($arResult['PAGE_ROUTE']['CONTENT_TYPE'] === $k ? 'selected' : ''); ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span class="adm-required-field"><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_ROUTE_PATH') ?>:</span>
        </td>
        <td width="60%">
            <input type="text" style="max-width: 300px; width: 300px;" name="ROUTE_PATH" value="<?=htmlspecialcharsbx((string)$arResult['PAGE_ROUTE']['ROUTE_PATH']);?>" required/>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_COMPONENT_HEADER') ?>:</span>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_COMPONENT_NAME') ?>:</span>
        </td>
        <td width="60%">
            <input type="text" style="max-width: 300px; width: 300px;" name="COMPONENT_NAME" value="<?=htmlspecialcharsbx((string)$arResult['PAGE_ROUTE']['COMPONENT_NAME']);?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_COMPONENT_TEMPLATE') ?>:</span>
        </td>
        <td width="60%">
            <input type="text" style="max-width: 300px; width: 300px;" name="COMPONENT_TEMPLATE" value="<?=htmlspecialcharsbx((string)$arResult['PAGE_ROUTE']['COMPONENT_TEMPLATE']);?>"/>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_COMPONENT_PARAMS') ?>:</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea cols="60" name="COMPONENT_PARAMS" id="COMPONENT_PARAMS" rows="15" style="width:100%;"><?= htmlspecialcharsbx((string)$arResult['PAGE_ROUTE']['COMPONENT_PARAMS_JSON']); ?></textarea>
            <br/>
            <?php
            CCodeEditor::Show(array(
                'textareaId' => 'COMPONENT_PARAMS',
                'height' => 350,
                'forceSyntax' => 'json',
            ));
            ?>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_HTML_CONTENT') ?>:</span>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <?php \CFileMan::AddHTMLEditorFrame(
                'HTML_CONTENT',
                $arResult['PAGE_ROUTE']['HTML_CONTENT'] ?? '',
                'HTML_CONTENT_TYPE',
                'html',
                ['height' => 450, 'width' => '100%'],
                'N',
                0,
                '',
                '',
                SITE_ID,
                true,
                false,
                [
                    'toolbarConfig' => \CFileMan::GetEditorToolbarConfig('route_admin'),
                ],
            ); ?>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_INCLUDE_HEADER') ?>:</span>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_INCLUDE_PATH') ?>:</span>
        </td>
        <td width="60%">
            <input type="text" style="max-width: 300px; width: 300px;" name="INCLUDE_PATH" value="<?=htmlspecialcharsbx((string)$arResult['PAGE_ROUTE']['INCLUDE_PATH']);?>"/>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_REDIRECT_HEADER') ?>:</span>
        </td>
    </tr>
    <tr>
        <td width="40%">
            <span><?= Loc::getMessage('CRASIVO_PAGES_EDIT_FORM_ROUTE_REDIRECT_URL') ?>:</span>
        </td>
        <td width="60%">
            <input type="text" style="max-width: 300px; width: 300px;" name="REDIRECT_URL" value="<?=htmlspecialcharsbx((string)$arResult['PAGE_ROUTE']['REDIRECT_URL']);?>"/>
        </td>
    </tr>
    <?php $tabControl->Buttons(['ajaxMode' => false, 'disabled' => !$component->hasModuleRight('W')]); ?>
    <?php $tabControl->End(); ?>
</form>
