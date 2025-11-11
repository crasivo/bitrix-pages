<?php

declare(strict_types=1);

namespace Crasivo\Pages\Integration\Rest\Event;

use Bitrix\Main\Context;
use Bitrix\Main\Config\Option;

/**
 * NOTES:
 * - Обработчик чем-то похож на OAuth (см.ниже) и совместим с ним по структуре запроса.
 * - Дальнейшая логика (права доступа) должны определяться и регулироваться в других слоях.
 *
 * @internal
 * @see \Bitrix\Rest\OAuth\Auth
 */
final class OnRestCheckAuth
{
    /** @var string */
    public const AUTH_PARAM_NAME = 'access_token';

    /** @var string */
    public const MODULE_ID = 'crasivo.pages';

    /** @var string */
    public const MODULE_SCOPE = 'crasivo:pages';

    /**
     * Execute event handler.
     *
     * @param array $query
     * @param string $scope
     * @param array $res
     * @return bool|null
     */
    public static function do(array $query, $scope, &$res): ?bool
    {
        // check request
        if (!is_string($scope) || !str_starts_with($scope, self::MODULE_SCOPE)) {
            return null;
        }
        if (!($authToken = self::getAuthTokenFromRequest($query))) {
            return null;
        }

        try {
            // todo: обернуть настройки модуля в отдельную модель/реестр
            $moduleOptions = Option::getForModule(self::MODULE_ID);
            if ('Y' !== $moduleOptions['rest_auth_enabled']) {
                return null;
            }

            // check auth token
            if ($authToken !== $moduleOptions['rest_auth_token']) {
                $res = [
                    'error' => 'INVALID_ACCESS_TOKEN',
                    'error_description' => 'Invalid access token',
                ];

                return false;
            }

            // check user
            if (!is_numeric($moduleOptions['rest_auth_user']) || $moduleOptions['rest_auth_user'] < 1) {
                $res = [
                    'error' => 'INVALID_USER_ID',
                    'error_description' => 'Invalid user ID',
                ];

                return false;
            }

            /** @global \CUser $USER */
            global $USER;
            if ($USER instanceof \CUser && $USER->Authorize((int)$moduleOptions['rest_auth_user'], false, false)) {
                return true;
            }
        } catch (\Throwable $exception) {
            // note: фатальные ошибки нас не интересуют
        }

        return null;
    }

    /**
     * Returns the token value from the HTTP Request.
     *
     * @param array $query
     * @return string|null
     */
    private static function getAuthTokenFromRequest(array $query): ?string
    {
        // check query
        if (array_key_exists(self::AUTH_PARAM_NAME, $query)) {
            return $query[self::AUTH_PARAM_NAME];
        }

        // check header
        $authHeader = Context::getCurrent()
            ->getRequest()
            ->getHeader('Authorization');
        if (!is_string($authHeader) || $authHeader === '') {
            return null;
        }

        // check & parse value
        $authHeader = preg_replace('/^Bearer\s+/i', '', $authHeader);
        if (is_string($authHeader) && $authHeader !== '') {
            return $authHeader;
        }

        return null;
    }
}
