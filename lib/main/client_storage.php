<?php

namespace DK\Helper\Main;

class ClientStorage
{

    public static function set($strName, $mixValue)
    {
        global $APPLICATION;

        $_SESSION[$strName] = $mixValue;
        $APPLICATION->set_cookie($strName, $mixValue);
    }

    public static function get($strName, $mixDefault)
    {
        global $APPLICATION;

        $mixValue = $APPLICATION->get_cookie($strName);

        if (isset($_SESSION[$strName])) {
            $mixValue = $_SESSION[$strName];
        }

        if ($mixValue === '' || is_null($mixValue)) {
            $mixValue = $mixDefault;
        }

        return $mixValue;
    }
}