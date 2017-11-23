<?php

namespace DK\Helper\Iblock;

use Bitrix\Main\Loader;

Loader::includeModule('iblock');


class Property
{
    static public function getIdByName($intIblockID, $strName, $arParams = array())
    {
        $arFilter = array(
            'IBLOCK_ID' => $intIblockID,
            'NAME' => $strName
        );
        $dbProperty = \CIBlockProperty::GetList(array(), $arFilter);
        if ($arFields = $dbProperty->GetNext()) {
            return $arFields['ID'];
        }

        // add property
        $arFields = Array(
            'IBLOCK_ID' => $intIblockID,
            'NAME' => $strName
        );
        if (!empty($arParams)) {
            $arFields = array_merge($arFields, $arParams);
        }

        $obProperty = new \CIBlockProperty;
        $intId = $obProperty->Add($arFields);

        return $intId;
    }

    static public function getIdEnumValue($intIblockID, $strPropertyName, $strValueName)
    {
        // get property
        $arParams = array(
            'PROPERTY_TYPE' => 'L',
        );
        $intPropertyID = self::getIdByName($intIblockID, $strPropertyName, $arParams);

        // get value
        $intValueID = 0;
        if ($intPropertyID !== false) {
            $arFilter = array(
                'VALUE' => $strValueName,
                'IBLOCK_ID' => $intIblockID,
                'PROPERTY_ID' => $intPropertyID
            );
            $dbEnum = \CIBlockPropertyEnum::GetList(array(), $arFilter);
            if ($arFields = $dbEnum->GetNext()) {
                $intValueID = $arFields['ID'];
            }

            if (empty($intValueID)) {
                $obEnum = new \CIBlockPropertyEnum;
                $arFields = array(
                    'PROPERTY_ID' => $intPropertyID,
                    'VALUE' => $strValueName
                );
                $intValueID = $obEnum->Add($arFields);
            }
        }

        $arOut = array(
            'PROPERTY' => $intPropertyID,
            'VALUE' => $intValueID
        );

        return $arOut;
    }

    static public function getValuesPropertyString($intIblockID, $strPropertyCode)
    {
        $arFilter = array(
            'IBLOCK_ID' => $intIblockID,
            'ACTIVE' => 'Y'
        );
        $arSelect = array(
            'PROPERTY_' . $strPropertyCode,
        );
        $dbElement = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arResult = array();
        while ($arFields = $dbElement->GetNext()) {
            $intID = $arFields['PROPERTY_' . $strPropertyCode . '_VALUE_ID'];
            $arResult[$intID] = $arFields['PROPERTY_' . $strPropertyCode . '_VALUE'];
        }

        return $arResult;
    }

    static public function getValuesPropertyEnum($intIblockID, $strPropertyCode)
    {
        $arResult = array();

        $arFilter = array(
            'IBLOCK_ID' => $intIblockID,
            'PROPERTY_ID' => $strPropertyCode,
        );
        $dbEnum = \CIBlockPropertyEnum::GetList(array(), $arFilter);
        while ($arFields = $dbEnum->GetNext()) {
            $arResult[$arFields['ID']] = $arFields['VALUE'];
        }

        return $arResult;
    }
}