<?php

namespace DK\Helper\Sale;

use Bitrix\Main\Loader;

Loader::includeModule('iblock');
Loader::includeModule('catalog');

class Price
{
    public static function setMinMax($intIblockElementID, $intCatalogGroupID, $strMaxPropertyName = 'MAXIMUM_PRICE', $strMinPropertyName = 'MINIMUM_PRICE')
    {

        $dbElement = \CIblockElement::GetByID($intIblockElementID);
        $arElement = $dbElement->Fetch();

        if (is_array($arElement)) {

            $arOffers = \CIBlockPriceTools::GetOffersArray(
                array(
                    'IBLOCK_ID' => $arElement['IBLOCK_ID']
                ),
                array($arElement['ID']),
                array(),
                array('ID')
            );

            $arOffersIDs = array();

            foreach ($arOffers as $arOffer) {
                $arOffersIDs[] = $arOffer['ID'];
            }

            $arFields = array(
                'CATALOG_GROUP_ID' => $intCatalogGroupID,
                'PRODUCT_ID' => $arOffersIDs
            );

            $arMinimum = \CPrice::GetList(array(), $arFields, array('MIN' => 'PRICE'))->Fetch();
            $arMaximum = \CPrice::GetList(array(), $arFields, array('MAX' => 'PRICE'))->Fetch();

            \CIBlockElement::SetPropertyValuesEx($arElement['ID'], false, array($strMinPropertyName => $arMinimum['PRICE']));
            \CIBlockElement::SetPropertyValuesEx($arElement['ID'], false, array($strMaxPropertyName => $arMaximum['PRICE']));
        }
    }

    static public function add($intProductID, $intPriceTypeID, $floatPrice, $strCurrency = 'RUB')
    {
        $arFields = array(
            'PRODUCT_ID' => $intProductID,
            'CATALOG_GROUP_ID' => $intPriceTypeID,
            'CURRENCY' => $strCurrency
        );

        $dbPrice = \CPrice::GetList(array(), $arFields);

        $arFields['PRICE'] = $floatPrice;
        if ($arPrice = $dbPrice->GetNext()) {
            \CPrice::Update($arPrice['ID'], $arFields);
        } else {
            \CPrice::Add($arFields);
        }
    }
}