<?php

namespace DK\Helper\Others;

class Struct
{
    public static function menu(&$arResult, $intLevel = 1)
    {
        $arNewStruct = array();

        while (count($arResult) > 0) {
            if ($arResult[0]['DEPTH_LEVEL'] != $intLevel) break;
            $arItem = array_shift($arResult);
            if ($arItem['IS_PARENT']) {
                $arItem['CHILDREN'] = self::menu($arResult, ($arItem['DEPTH_LEVEL'] + 1));
            }
            $arNewStruct[] = $arItem;
        }

        return $arNewStruct;
    }
}