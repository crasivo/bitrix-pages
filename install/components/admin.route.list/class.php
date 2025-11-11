<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\ORM\Fields\ScalarField;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Crasivo\Pages\Integration\Database\ORM\PageRouteTable;

Loc::loadMessages(__FILE__);

/**
 * @internal
 */
class CrasivoPagesAdminRouteListComponent extends \CBitrixComponent
{
    /** @var string */
    public const MODULE_ID = 'crasivo.pages';

    /**
     * Возвращает флаг наличия у текущего пользователя минимального набора прав.
     *
     * @param string $right
     * @return bool
     */
    public function hasModuleRight(string $right): bool
    {
        return $GLOBALS['APPLICATION']->GetGroupRight(self::MODULE_ID) >= $right
            || $GLOBALS['USER']->IsAdmin();
    }

    /**
     * @inheritDoc
     */
    public function executeComponent()
    {
        try {
            // include module & check rights
            Loader::requireModule(self::MODULE_ID);
            if (!$this->hasModuleRight('R')) {
                $GLOBALS['APPLICATION']->AuthForm('');
                return false;
            }

            // prepare sort
            $tableId = PageRouteTable::getTableName();

            // UI: Define & prepare sorting
            $adminUiSorting = new \CAdminUiSorting($this->arParams['UI_LIST_ID'], PageRouteTable::COLUMN_ID, 'ASC');
            $sortBy = mb_strtoupper($adminUiSorting->getField());
            $sortOrder = mb_strtoupper($adminUiSorting->getOrder());
            $ormSortOrder = [ PageRouteTable::COLUMN_ID => $sortOrder ];
            if ($sortBy !== PageRouteTable::COLUMN_ID) {
                $ormSortOrder[PageRouteTable::COLUMN_ID] = 'ASC';
            }

            // UI: Start build list
            $adminUiList = new CAdminUiList($this->arParams['UI_LIST_ID'], $adminUiSorting);

            // UI: Build list filter
            $adminFilterFields = $this->getAdminUiListFilterFields();
            $ormFilter = [];
            $adminUiList->AddFilter($adminFilterFields, $ormFilter);

            // UI: Build list headers
            $adminHeaderFields = $this->getAdminUiListHeaderFields();
            $adminUiList->AddHeaders($adminHeaderFields);
            $ormSelectFields = array_map(fn(array $f) => $f['id'], $adminHeaderFields);
            $ormSelectFieldsMap = array_fill_keys($ormSelectFields, true);

            $adminSelectFields = $adminUiList->GetVisibleHeaderColumns();
            if (!in_array(PageRouteTable::COLUMN_ID, $adminSelectFields)) {
                $adminSelectFields[] = PageRouteTable::COLUMN_ID;
            }
            $adminSelectFieldsMap = array_fill_keys($adminSelectFields, true);
            $adminSelectFieldsMap = array_merge($ormSelectFieldsMap, $adminSelectFieldsMap);

            // UI: Build query result
            $ormQueryResult = PageRouteTable::getList([
                'filter' => $ormFilter,
                'order' => $ormSortOrder,
                'select' => $ormSelectFields,
            ]);

            // UI: Build final result
            $adminUiResult = new CAdminUiResult($ormQueryResult, $tableId);
            $adminUiResult->NavStart();
            $adminUiList->SetNavigationParams($adminUiResult, [
                'BASE_LINK' => $this->arParams['ROUTE_LIST_URL'],
            ]);

            /** @global CAdminSidePanelHelper $adminSidePanelHelper */
            global $adminSidePanelHelper;
            $this->buildAdminUiListRows(
                $adminSelectFieldsMap,
                $adminUiResult,
                $adminUiList,
                $adminSidePanelHelper,
            );

            // Finish
            $adminUiList->AddAdminContextMenu($this->getAdminUiContextMenu(), false);
            $adminUiList->CheckListMode();
            $adminUiList->DisplayFilter($adminFilterFields);
            $adminUiList->DisplayList();
        } catch (\Throwable $exception) {
            $this->arResult['ERROR'] = $exception->getMessage();
        }
    }

    /**
     * @inheritDoc
     */
    public function onPrepareComponentParams($arParams)
    {
        // Cache: disable permanent
        $arParams['CACHE_TIME'] = 0;
        $arParams['CACHE_TYPE'] = 'N';

        // UI
        $arParams['UI_LIST_ID'] = is_string($arParams['UI_LIST_ID'])
            ? $arParams['UI_LIST_ID']
            : 'crasivo_pages_route_list';

        // Page
        $arParams['PAGE_TITLE'] = is_string($arParams['PAGE_TITLE'])
            ? $arParams['PAGE_TITLE']
            : Loc::getMessage('CRASIVO_PAGES_ROUTE_LIST_PAGE_TITLE');

        // Links
        $arParams['ROUTE_LIST_URL'] = is_string($arParams['ROUTE_LIST_URL'])
            ? $arParams['ROUTE_LIST_URL']
            : '/bitrix/admin/crasivo_pages_route_list.php';
        $arParams['ROUTE_EDIT_URL'] = is_string($arParams['ROUTE_EDIT_URL'])
            ? $arParams['ROUTE_EDIT_URL']
            : '/bitrix/admin/crasivo_pages_route_edit.php';

        return $arParams;
    }

    /**
     * @param array $adminSelectFieldsMap
     * @param \CAdminUiResult $adminUiResult
     * @param CAdminUiList $adminUiList
     * @param CAdminSidePanelHelper $adminSidePanelHelper
     * @return array[]
     */
    public function buildAdminUiListRows(
        array $adminSelectFieldsMap,
        \CAdminUiResult $adminUiResult,
        \CAdminUiList $adminUiList,
        \CAdminSidePanelHelper $adminSidePanelHelper,
    ): array
    {
        $adminUiListRows = [];
        $userCanEdit = $this->hasModuleRight('W');
        $userCanDelete = $this->hasModuleRight('X');

        while ($item = $adminUiResult->Fetch()) {
            $itemEditUrl = $adminSidePanelHelper->editUrlToPublicPage(sprintf(
                $this->arParams['ROUTE_EDIT_URL'] . '?ID=%dlang=%s',
                $item[PageRouteTable::COLUMN_ID],
                LANGUAGE_ID,
            ));

            $adminUiListRows[$item[PageRouteTable::COLUMN_ID]] = $row =& $adminUiList->AddRow(
                $item[PageRouteTable::COLUMN_ID],
                $item,
                $itemEditUrl,
            );

            $row->AddField(PageRouteTable::COLUMN_ID, sprintf('<a href="%s">%s</a>', $itemEditUrl, $item[PageRouteTable::COLUMN_ID]));

            if (isset($adminSelectFieldsMap[PageRouteTable::COLUMN_ACTIVE])) {
                $row->AddCheckField(PageRouteTable::COLUMN_ACTIVE);
            }
            if (isset($adminSelectFieldsMap[PageRouteTable::COLUMN_CONTENT_TYPE])) {
                $row->AddInputField(PageRouteTable::COLUMN_ROUTE_PATH);
            }
            if (isset($adminSelectFieldsMap[PageRouteTable::COLUMN_ROUTE_PATH])) {
                $row->AddInputField(PageRouteTable::COLUMN_ROUTE_PATH);
            }
            if (isset($adminSelectFieldsMap[PageRouteTable::COLUMN_COMPONENT_NAME])) {
                $row->AddInputField(PageRouteTable::COLUMN_COMPONENT_NAME);
            }
            if (isset($adminSelectFieldsMap[PageRouteTable::COLUMN_COMPONENT_TEMPLATE])) {
                $row->AddInputField(PageRouteTable::COLUMN_COMPONENT_TEMPLATE);
            }
            if (isset($adminSelectFieldsMap[PageRouteTable::COLUMN_INCLUDE_PATH])) {
                $row->AddInputField(PageRouteTable::COLUMN_INCLUDE_PATH);
            }
            if (isset($adminSelectFieldsMap[PageRouteTable::COLUMN_REDIRECT_URL])) {
                $row->AddInputField(PageRouteTable::COLUMN_REDIRECT_URL);
            }

            // define row actions
            if (!$userCanEdit) {
                continue;
            }

            // todo: добавить дополнительные действия
            $rowActions = [];
            $rowActions[] = [
                'ICON' => 'edit',
                'TEXT' => Loc::getMessage('CRASIVO_PAGES_ROUTE_LIST_ROW_EDIT'),
                'LINK' => $itemEditUrl,
                'DEFAULT' => true,
            ];

            $row->AddActions($rowActions);
        }

        return $adminUiListRows;
    }

    /**
     * @see \CAdminUiList::AddAdminContextMenu
     * @return array[]
     */
    protected function getAdminUiContextMenu(): array
    {
        if (!$this->hasModuleRight('W')) {
            return [];
        }

        $contextMenu = [];
        $contextMenu[] = [
            'TEXT' => 'Создать маршрут',
            'LINK' => sprintf(
                $this->arParams['ROUTE_EDIT_URL'] . '?lang=%s',
                LANGUAGE_ID,
            ),
            'ICON' => 'btn_new',
            'TITLE' => 'Создать новый маршрут',
        ];

        return $contextMenu;
    }

    /**
     * @see \CAdminUiList::AddHeaders
     * @return array[]
     * @throws \Throwable
     */
    public function getAdminUiListHeaderFields()
    {
        $ormScalarFields = $this->getOrmPageRouteScalarFields();
        $resultFields = [];

        foreach ($ormScalarFields as $scalarField) {
            if ($scalarField instanceof \Bitrix\Main\ORM\Fields\TextField) {
                continue;
            }

            $name = $scalarField->getName();
            $resultFields[] = [
                'id' => $name,
                'content' => $scalarField->getTitle() ?? $name,
                'sort' => $name,
                'default' => $scalarField->isRequired(),
            ];
        }

        return $resultFields;
    }

    /**
     * @see \CAdminUiList::AddFilter
     * @return array[]
     * @throws \Throwable
     */
    public function getAdminUiListFilterFields(): array
    {
        $ormScalarFields = $this->getOrmPageRouteScalarFields();
        $resultFields = [];
        foreach ($ormScalarFields as $field) {
            if ($field instanceof \Bitrix\Main\ORM\Fields\TextField) {
                continue;
            }

            $name = $field->getName();
            $filterField = [
                'id' => $name,
                'name' => $field->getTitle() ?? $name,
                'type' => 'string',
                'filterable' => '%',
                'querySearch' => '%',
                'default' => $field->isRequired(),
            ];

            if ($field instanceof \Bitrix\Main\ORM\Fields\StringField) {
                $resultFields[] = $filterField;
                continue;
            }
            if ($field instanceof \Bitrix\Main\ORM\Fields\IntegerField) {
                $filterField['type'] = 'number';
                $filterField['filterable'] = '=';
                $filterField['querySearch'] = '=';
                $resultFields[] = $filterField;
                continue;
            }
            if ($field instanceof \Bitrix\Main\ORM\Fields\EnumField) {
                $filterField['type'] = 'list';
                $filterField['filterable'] = '=';
                $filterField['querySearch'] = '=';
                $filterField['items'] = array_combine($field->getValues(), $field->getValues());
                $resultFields[] = $filterField;
                continue;
            }
            if ($field instanceof \Bitrix\Main\ORM\Fields\BooleanField) {
                $filterField['type'] = 'list';
                $filterField['filterable'] = '=';
                $filterField['querySearch'] = '=';
                $filterField['items'] = [
                    'Y' => 'Да',
                    'N' => 'Нет',
                ];
                $resultFields[] = $filterField;
            }
        }

        return $resultFields;
    }

    /**
     * Возвращает полный список скалярных полей из ORM класса.
     *
     * @return ScalarField[]
     * @throws \Throwable
     */
    protected function getOrmPageRouteScalarFields(): array
    {
        return array_filter(PageRouteTable::getMap(), function ($field) {
            return $field instanceof ScalarField;
        });
    }
}
