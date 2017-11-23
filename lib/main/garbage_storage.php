<?php

namespace DK\Helper\Main;

class GarbageStorage
{
    private static $arStorage = array();

    public static function set($strName, $mixValue)
    {
        self::$arStorage[$strName] = $mixValue;
    }

    public static function get($strName)
    {
        return self::$arStorage[$strName];
    }
}