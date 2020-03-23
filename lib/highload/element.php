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
     * @param $strIblockData
     * @param $arFilter
     * @param $arSelect
     * @param $intLimit
     * @return array
     */
    public static function getElement($strIblockData, $arFilter = array(), $arSelect = array(), $intLimit = 0)
    {
        $strEntityDataClass = self::getEntityDataClass($strIblockData);

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

    public static function update($strIblockData, $intElementID, $arUpdate)
    {
        $strEntityDataClass = self::getEntityDataClass($strIblockData);
        $obResult = $strEntityDataClass::update($intElementID, $arUpdate);

        if (!$obResult->isSuccess()) {
            throw new SystemException($obResult->getErrorMessages());
        }

        return true;
    }

    public static function add($strIblockData, $arFields)
    {
        $strEntityDataClass = self::getEntityDataClass($strIblockData);
        $obResult = $strEntityDataClass::add($arFields);

        if (!$obResult->isSuccess()) {
            throw new SystemException($obResult->getErrorMessages());
        }

        return $obResult->getId();
    }

    public static function delete($strIblockData, $intElementID)
    {
        $strEntityDataClass = self::getEntityDataClass($strIblockData);
        $obResult = $strEntityDataClass::delete($intElementID);

        if (!$obResult->isSuccess()) {
            throw new SystemException($obResult->getErrorMessages());
        }

        return true;
    }

    public static function getAllFieldsByReference($arReference)
    {
        if (empty($arReference['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($arReference['VALUE'])) {
            throw new SystemException('wrong reference');
        }
        $arFilter = array(
            'UF_XML_ID' => $arReference['VALUE']
        );
        $arFields = self::getElement($arReference['USER_TYPE_SETTINGS']['TABLE_NAME'], $arFilter);
        $arFields = $arFields[0];
        if (is_numeric($arFields['UF_FILE'])) {
            $arFields['UF_FILE'] = \CFile::GetPath($arFields['UF_FILE']);
        }

        $arReference['VALUE_DATA'] = $arFields;

        return $arReference;
    }

    public static function getEntityDataClass($strIblockData)
    {
        if (is_numeric($strIblockData)) {
            $arHLBlock = HighloadBlockTable::getById($strIblockData)->fetch();
        } else {
            $arHLBlock = HighloadBlockTable::getList(array('filter' => array('TABLE_NAME' => $strIblockData)))->fetch();
        }

        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();

        return $strEntityDataClass;
    }
}
