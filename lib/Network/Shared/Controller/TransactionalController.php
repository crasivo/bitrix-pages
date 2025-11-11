<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Controller;

use Bitrix\Main\Application;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Request;

/**
 * NOTES:
 * - предполагается использовать в контроллерах, у которых есть запросы в базу (insert/update)
 *
 * @internal
 * @link https://docs.1c-bitrix.ru/pages/database/transactions.html
 */
abstract class TransactionalController extends Controller
{
    /** @var Connection */
    protected Connection $dbConnection;

    /**
     * Controller constructor.
     *
     * @param Request|null $request
     * @throws \Throwable
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->dbConnection = Application::getConnection();
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    protected function processAfterAction(Action $action, $result)
    {
        $this->dbConnection->commitTransaction();

        return parent::processAfterAction($action, $result);
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    protected function processBeforeAction(Action $action)
    {
        $this->dbConnection->startTransaction();

        return parent::processBeforeAction($action);
    }

    /**
     * @inheritDoc
     * @throws \Throwable
     */
    protected function runProcessingThrowable(\Throwable $throwable)
    {
        try {
            $this->dbConnection->rollbackTransaction();
        } finally {
            parent::runProcessingThrowable($throwable);
        }
    }
}
