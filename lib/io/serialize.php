<?php

namespace DK\Helper\IO;

class Serialize
{
    public static function write($strName, $obData)
    {
        $strPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $strName . '.dat';
        $strData = serialize($obData);
        file_put_contents($strPath, $strData);

        return true;
    }

    public static function ride($strName)
    {
        $strPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $strName . '.dat';
        $strData = file_get_contents($strPath);
        $obData = unserialize($strData);

        return $obData;
    }
}