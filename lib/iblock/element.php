<?php

namespace DK\Helper\Iblock;

use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;

Loader::includeModule('iblock');

class Element
{
    private static $arElements;

    public static function delete($arIDs)
    {
        if (!is_array($arIDs)) {
            $arIDs = array($arIDs);
        }

        foreach ($arIDs as $intID) {
            \CIBlockElement::Delete($intID);
        }

        return true;
    }

    public static function getFieldsByID($intElementID, $strFieldName = '', $bRefresh = false)
    {
        if (empty($intElementID)) {
            throw new SystemException('$intElementID is required');
        }

        if (!empty(self::$arElements[$intElementID]['FIELDS']) && $bRefresh === false) {
            $arFields = self::$arElements[$intElementID]['FIELDS'];
            if (empty($strFieldName)) {
                return $arFields;
            } else {
                return $arFields[$strFieldName];
            }
        }

        $dbElement = \CIBlockElement::GetByID($intElementID);

        if ($obElement = $dbElement->GetNextElement()) {
            $arFields = $obElement->GetFields();
            self::$arElements[$intElementID]['FIELDS'] = $arFields;

            if (empty($strFieldName)) {
                return $arFields;
            } else {
                return $arFields[$strFieldName];
            }
        }
        return false;
    }

    public static function getPropertiesByID($intElementID, $strPropertyName = '', $bRefresh = false)
    {
        if (empty($intElementID)) {
            throw new SystemException('$intElementID is required');
        }

        if (!empty(self::$arElements[$intElementID]['PROPERTY']) && $bRefresh === false) {
            $arProperty = self::$arElements[$intElementID]['PROPERTY'];
            if (empty($strPropertyName)) {
                return $arProperty;
            } else {
                return $arProperty[$strPropertyName];
            }
        }

        $dbElement = \CIBlockElement::GetByID($intElementID);

        if ($obElement = $dbElement->GetNextElement()) {
            $arProperty = $obElement->GetProperties();
            self::$arElements[$intElementID]['PROPERTY'] = $arProperty;

            if (empty($strPropertyName)) {
                return $arProperty;
            } else {
                return $arProperty[$strPropertyName];
            }
        }
        return false;
    }

    public static function deactivate($arIDs)
    {
        return self::update($arIDs, array('ACTIVE' => 'N'));
    }

    public static function activate($arIDs)
    {
        return self::update($arIDs, array('ACTIVE' => 'Y'));
    }

    private static function update($arIDs, $arFields)
    {
        if (!is_array($arIDs)) {
            $arIDs = array($arIDs);
        }

        $obElement = new \CIBlockElement;
        foreach ($arIDs as $intID) {
            if (!$obElement->Update($intID, $arFields)) {
                throw new SystemException($obElement->LAST_ERROR);
            }
        }

        return true;
    }
}