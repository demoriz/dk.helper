<?php

namespace DK\Helper\IO;

class File
{
    public static function getHRSize($intByte)
    {
        $arFormats = array('Б', 'КБ', 'МБ', 'ГБ', 'ТБ');
        $intFormat = 0;
        $intFileSize = 0;

        while ($intByte > 1024 && count($arFormats) != ++$intFormat) {
            $intFileSize = round($intByte / 1024, 2);
        }

        $arFormats[] = 'ТБ';

        return $intFileSize . $arFormats[$intFormat];
    }
}