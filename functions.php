<?php

if (!function_exists('dd')) {
    function dd(...$values)
    {
        echo '<pre>';
        var_dump(...$values);
        echo '</pre>';
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        if (false === ($val = getenv($key))) {
            return $default;
        }

        return match (true) {
            is_numeric($val) => str_contains($val, '.') ? floatval($val) : intval($val),
            $val === 'true' => true,
            $val === 'false' => false,
            $val === 'null' => null,
            default => $default,
        };
    }
}

if (!function_exists('request')) {
    function request()
    {
        return \Bitrix\Main\Context::getCurrent()->getRequest();
    }
}

if (!function_exists('service')) {
    function service(string $key, $default = null)
    {
        try {
            return \Bitrix\Main\DI\ServiceLocator::getInstance()->get($key);
        } catch (\Bitrix\Main\DI\Exception\ServiceNotFoundException) {
            return $default;
        }
    }
}
