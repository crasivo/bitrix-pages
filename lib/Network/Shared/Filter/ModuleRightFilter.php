<?php

declare(strict_types=1);

namespace Crasivo\Pages\Network\Shared\Filter;

use Bitrix\Main\Context;
use Bitrix\Main\Engine\ActionFilter\Base as BaseActionFilter;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

/**
 * @final
 * @internal
 */
class ModuleRightFilter extends BaseActionFilter
{
    /** @var string */
    public const READ = 'R';

    /** @var string */
    public const WRITE = 'W';

    /**
     * Action filter constructor.
     *
     * @param string $minimalRight
     */
    public function __construct(
        protected readonly string $minimalRight,
    )
    {
        parent::__construct();
    }

    /**
     * Check action before run.
     *
     * @param Event $event
     * @return EventResult|null
     */
    public function onBeforeAction(Event $event)
    {
        /** @global \CMain $APPLICATION */
        /** @global \CUser $USER */

        global $USER;
        if ($USER instanceof \CUser && $USER->IsAdmin()) {
            return null;
        }

        global $APPLICATION;
        $moduleRight = $APPLICATION->GetGroupRight(CRASIVO_PAGES_MODULE_ID);
        if ($moduleRight >= $this->minimalRight) {
            return null;
        }

        $this->addError(new Error(
            message: sprintf('Minimum access rights "%s" are required to work with the module.', $this->minimalRight),
            code: 401,
        ));

        Context::getCurrent()
            ->getResponse()
            ->setStatus(401);

        return new EventResult(EventResult::ERROR);
    }
}