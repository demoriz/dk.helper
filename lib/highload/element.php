<?php

namespace DK\Helper\Highload;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;

Loader::includeModule('highloadblock');

/**
 * Class Element
 * @package DK\Helper\Highload
 */
class Element
{
    /**
     * @param $intIblockID
     * @param $arFilter
     * @param $arSelect
     * @param $intLimit
     * @return array
     */
    public static function getElement($intIblockID, $arFilter = array(), $arSelect = array(), $intLimit = 0)
    {
        $strEntityDataClass = self::getEntityDataClass($intIblockID);

        $arQuery = array();

        if (!empty($arFilter)) {
            $arQuery['filter'] = $arFilter;
        }

        if (!empty($arSelect)) {
            $arQuery['select'] = $arSelect;
        }

        if (is_numeric($intLimit) && $intLimit > 0) {
            $arQuery['limit'] = $intLimit;
        }

        $dbData = $strEntityDataClass::getList($arQuery);

        $arElements = $dbData->fetchAll();

        return $arElements;
    }

    public static function update($intIblockID, $intElementID, $arUpdate)
    {
        $strEntityDataClass = self::getEntityDataClass($intIblockID);
        $obResult = $strEntityDataClass::update($intElementID, $arUpdate);

        if (!$obResult->isSuccess()) {
            throw new SystemException($obResult->getErrorMessages());
        }

        return true;
    }

    public static function add($intIblockID, $arFields)
    {
        $strEntityDataClass = self::getEntityDataClass($intIblockID);
        $obResult = $strEntityDataClass::add($arFields);

        if (!$obResult->isSuccess()) {
            throw new SystemException($obResult->getErrorMessages());
        }

        return $obResult->getId();
    }

    private static function getEntityDataClass($intIblockID)
    {
        $arHLBlock = HighloadBlockTable::getById($intIblockID)->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        return $strEntityDataClass;
    }
}
