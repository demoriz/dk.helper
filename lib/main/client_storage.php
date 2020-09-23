<?php

namespace DK\Helper\Main;

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

class ClientStorage
{

    public static function set($strName, $mixValue)
    {
        $strName = self::prepareName($strName);

        $_SESSION[$strName] = $mixValue;

        $cookie = new Cookie($strName, $mixValue);
        Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
    }

    public static function get($strName, $mixDefault)
    {
        $strName = self::prepareName($strName);
        $mixValue = Application::getInstance()->getContext()->getRequest()->getCookie($strName);

        if (isset($_SESSION[$strName])) {
            $mixValue = $_SESSION[$strName];
        }

        if ($mixValue === '' || is_null($mixValue)) {
            $mixValue = $mixDefault;
        }

        return $mixValue;
    }

    private static function prepareName($strName)
    {
        $strName = str_replace('.', '_', $strName);

        return $strName;
    }
}