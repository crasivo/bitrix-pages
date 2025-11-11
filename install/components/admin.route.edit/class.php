<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Crasivo\Pages\Domain\Api\PageContentType;
use Crasivo\Pages\Integration\Database\ORM\PageRouteTable;

Loc::loadMessages(__FILE__);

/**
 * @internal
 */
class CrasivoPagesAdminRouteEditComponent extends \CBitrixComponent
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
            Loader::requireModule(self::MODULE_ID);
            switch ($this->request->get('action') ?? 'show') {
                case 'delete':
                    $this->processDeleteRouteFromRequest();
                    break;
                case 'save':
                    $this->processSaveRouteFromRequest();
                    break;
                default:
                    $this->processShowEditForm();
                    break;
            }
        } catch (\Throwable $exception) {
            $this->arResult['ERROR'] = $exception->getMessage();
            $this->includeComponentTemplate();
        }
    }

    /**
     * @inheritDoc
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = 0;
        $arParams['CACHE_TYPE'] = 'N';
        $arParams['PAGE_TITLE'] = is_string($arParams['PAGE_TITLE'])
            ? $arParams['PAGE_TITLE']
            : Loc::getMessage('CRASIVO_PAGES_ROUTE_EDIT_PAGE_TITLE');
        $arParams['ROUTE_LIST_URL'] = is_string($arParams['ROUTE_LIST_URL'])
            ? $arParams['ROUTE_LIST_URL']
            : '/bitrix/admin/crasivo_pages_route_list.php';
        $arParams['ROUTE_EDIT_URL'] = is_string($arParams['ROUTE_EDIT_URL'])
            ? $arParams['ROUTE_EDIT_URL']
            : '/bitrix/admin/crasivo_pages_route_edit.php';

        return $arParams;
    }

    /**
     * @return array
     */
    protected function getDefaultRouteData(): array
    {
        return [
            PageRouteTable::COLUMN_ID => 0,
            PageRouteTable::COLUMN_CONTENT_TYPE => PageContentType::Custom->value,
            PageRouteTable::COLUMN_ROUTE_DOMAIN => $_SERVER['SERVER_NAME'] ?? 'localhost',
            PageRouteTable::COLUMN_ROUTE_PATH => '/pages/custom',
            PageRouteTable::COLUMN_COMPONENT_NAME => null,
            PageRouteTable::COLUMN_COMPONENT_PARAMS => null,
            PageRouteTable::COLUMN_COMPONENT_TEMPLATE => null,
            PageRouteTable::COLUMN_HTML_CONTENT => null,
            PageRouteTable::COLUMN_INCLUDE_PATH => null,
            PageRouteTable::COLUMN_REDIRECT_URL => null,
        ];
    }

    /**
     * @param int|null $routeId
     * @return array[]
     */
    private function getFormContextMenuItems(?int $routeId = null): array
    {
        $arMenu = [
            [
                'TEXT' => 'Список маршрутов',
                'ICON' => 'btn_list',
                'LINK' => $this->arParams['ROUTE_LIST_URL'],
            ],
            [
                'SEPARATOR' => 'Y',
            ],
        ];

        if ($this->hasModuleRight('W')) {
            $arMenu[] = [
                'TEXT' => 'Создать новый',
                'ICON' => 'btn_new',
                'LINK' => (new \Bitrix\Main\Web\Uri($this->arParams['ROUTE_EDIT_URL']))
                    ->addParams(['ID' => 0, 'lang' => LANGUAGE_ID])
                    ->getUri(),
            ];
        }
        if ($routeId > 0 && $this->hasModuleRight('X')) {
            $arMenu[] = [
                'TEXT' => 'Удалить',
                'ICON' => 'btn_delete',
                'LINK' => (new \Bitrix\Main\Web\Uri($this->arParams['ROUTE_EDIT_URL']))
                    ->addParams(['action' => 'delete', 'ID' => $routeId, 'lang' => LANGUAGE_ID])
                    ->getUri(),
            ];
        }

        return $arMenu;
    }

    /**
     * @param int $id
     * @return array|null
     * @throws \Throwable
     */
    private function getRouteById(int $id): ?array
    {
        $pageRoute = PageRouteTable::getList([
            'filter' => [
                '=' . PageRouteTable::COLUMN_ID => $id,
            ],
            'limit' => 1,
            'select' => ['*'],
            'cache' => false,
        ])->fetch();

        return is_array($pageRoute) ? $pageRoute : null;
    }

    /**
     * @return mixed|void
     * @throws Throwable
     */
    private function processShowEditForm()
    {
        if (!$this->hasModuleRight('R')) {
            throw new \Exception('Текущему пользователю запрещено просматривать маршруты.');
        }

        $routeId = (int)$this->request->get('ID');
        $routeData = null;
        if ($routeId > 0) {
            $routeData = $this->getRouteById($routeId);
            if (!$routeData) {
                throw new \Exception('Route not found');
            }
        }

        $this->arResult['PAGE_ROUTE'] = $routeData ?? $this->getDefaultRouteData();

        // build context menu
        $this->arResult['CONTEXT_MENU'] = $this->getFormContextMenuItems($routeId);

        // build form actions
        $this->arResult['FORM_ACTION'] = (new \Bitrix\Main\Web\Uri($GLOBALS['APPLICATION']->GetCurPage()))
            ->addParams(['ID' => $routeData['ID'], 'lang' => LANGUAGE_ID]);
        $this->arResult['FORM_TABS'] = [
            [
                'DIV' => 'main',
                'TAB' => 'Основные',
                'ICON' => 'main',
                'TITLE' => 'Основные настройки маршрута',
            ],
        ];

        $this->includeComponentTemplate();
    }

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    private function processDeleteRouteFromRequest()
    {
        if (!$this->hasModuleRight('X')) {
            throw new \Exception('Текущему пользователю запрещено удалять маршруты.');
        }

        $routeId = $this->request->get('ID');
        if (!is_numeric($routeId) || $routeId <= 0) {
            throw new \Exception('Некорректный идентификатор маршрута (страницы).');
        }

        $serviceLocator = \Bitrix\Main\DI\ServiceLocator::getInstance();
        $pageRoute = $serviceLocator
            ->get(\Crasivo\Pages\Domain\Api\PageRouteFinder::class)
            ->getById($routeId);
        if (!$pageRoute) {
            throw new \Exception('Не удалось найти указанный маршрут (страницу).');
        }

        $serviceLocator
            ->get(\Crasivo\Pages\Domain\Api\RemovePageRoute::class)
            ->do($pageRoute);

        // permanent redirect
        \LocalRedirect((new \Bitrix\Main\Web\Uri($this->arParams['ROUTE_LIST_URL']))
            ->addParams(['lang' => LANGUAGE_ID])
            ->getUri());
    }

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    private function processSaveRouteFromRequest()
    {
        if (!$this->request->isPost()) {
            throw new \Exception('Invalid request method.');
        }
        if (!$this->hasModuleRight('W')) {
            throw new \Exception('Текущему пользователю запрещено изменять маршруты.');
        }

        $postData = $this->request
            ->getPostList()
            ->toArray();
        if (is_string($postData[PageRouteTable::COLUMN_COMPONENT_PARAMS])) {
            $postData[PageRouteTable::COLUMN_COMPONENT_PARAMS] = \Bitrix\Main\Web\Json::decode($postData[PageRouteTable::COLUMN_COMPONENT_PARAMS]);
        }

        $pageRoute = \Bitrix\Main\DI\ServiceLocator::getInstance()
            ->get(\Crasivo\Pages\Integration\Database\Api\SaveOrmPageRoute::class)
            ->do($postData);

        // permanent redirect
        \LocalRedirect((new \Bitrix\Main\Web\Uri($GLOBALS['APPLICATION']->GetCurPage()))
            ->addParams(['ID' => $pageRoute->getId(), 'lang' => LANGUAGE_ID])
            ->getUri());
    }
}
