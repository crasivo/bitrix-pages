<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @global \CMain $APPLICATION */
/** @global \CDatabase $DB */
/** @global \CUser $USER */
/** @var string $module_id */
/** @var string $REQUEST_METHOD */

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

// Load languages
Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');

// Define general constants
$module_id = 'crasivo.pages';
$moduleRights = $APPLICATION->GetGroupRight($module_id);
if ($moduleRights <= 'W' && !$USER->IsAdmin()) {
    $APPLICATION->AuthForm('');
}

// Require module
Loader::requireModule($module_id);

// Declare module options
$moduleLogLevels = [
    'debug' => Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_LEVEL_DEBUG'),
    'info' => Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_LEVEL_INFO'),
    'warning' => Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_LEVEL_WARNING'),
    'error' => Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_LEVEL_ERROR'),
];
$moduleOptionsDebug = [
    ['log_level', Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_LEVEL'), 'info', ['selectbox', $moduleLogLevels]],
    ['note' => Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_FILE_NOTE')],
    ['log_file_enabled', Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_FILE_ENABLED'), 'N', ['checkbox']],
    ['log_file_dir', Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_FILE_DIR'), '%upload_dir%/logs/' . $module_id, ['text']],
    ['log_file_count', Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_FILE_COUNT'), '10', ['text']],
    ['log_file_size', Loc::getMessage('CRASIVO_PAGES_SETTING_LOG_FILE_SIZE'), '10', ['text']],
];
$moduleOptionsRest = [
    Loc::getMessage('CRASIVO_PAGES_SETTING_REST_AUTH_TITLE'),
    ['rest_auth_enabled', Loc::getMessage('CRASIVO_PAGES_SETTING_REST_AUTH_ENABLED'), 'N', ['checkbox']],
    ['rest_auth_token', Loc::getMessage('CRASIVO_PAGES_SETTING_REST_AUTH_TOKEN'), \Bitrix\Main\Security\Random::getString(32), ['text']],
    ['rest_auth_user', Loc::getMessage('CRASIVO_PAGES_SETTING_REST_AUTH_USER'), $USER->GetID(), ['text']],
];
$moduleOptions = array_merge($moduleOptionsDebug, $moduleOptionsRest);

// Save data
$contextUrl = (new \Bitrix\Main\Web\Uri($APPLICATION->GetCurPage()))
    ->addParams(['mid' => $module_id, 'lang' => LANGUAGE_ID])
    ->getUri();
$request = Bitrix\Main\Context::getCurrent()->getRequest();
if ($request->isPost() && check_bitrix_sessid()) {
    foreach ($moduleOptions as $option) {
        list($key, $lang, $def) = (array)$option;
        if ($key === 'note' || !is_string($def)) {
            continue;
        }

        Option::set($module_id, $key, $request->getPost($key) ?? $def);
    }
}

// Declare form tabs
$formTabs = [
    ['DIV' => 'main', 'TAB' => 'Main', 'TITLE' => 'Main'],
    ['DIV' => 'rest', 'TAB' => 'REST', 'TITLE' => 'REST'],
    ['DIV' => 'rights', 'TAB' => 'Access rights', 'TITLE' => 'Access rights'],
];

// Build form
$adminTabControl = new CAdminTabControl('crasivo_pages', $formTabs, false, true);

?>
<form method="post" action="<?= $contextUrl ?>">
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?= LANGUAGE_ID; ?>">
<input type="hidden" name="ID" value="<?= $module_id; ?>">
<?php
    $adminTabControl->Begin();
    $adminTabControl->BeginNextTab();
    __AdmSettingsDrawList($module_id, $moduleOptionsDebug);
    $adminTabControl->BeginNextTab();
    __AdmSettingsDrawList($module_id, $moduleOptionsRest);
    $adminTabControl->BeginNextTab();
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');
    $adminTabControl->Buttons(['ajaxMode' => false, 'btnSave' => true, 'btnApply' => true]);
    echo bitrix_sessid_post();
    $adminTabControl->End();
?>
</form>
