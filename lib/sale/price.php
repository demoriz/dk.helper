<?php

namespace DK\Helper\Sale;

use Bitrix\Main\Loader;

Loader::includeModule('iblock');
Loader::includeModule('catalog');

class Price
{
    public static function setMinMaxByOffer($intOfferElementID, $intCatalogGroupID, $strMaxPropertyName = 'MAXIMUM_PRICE', $strMinPropertyName = 'MINIMUM_PRICE')
    {

        $arElement = \CCatalogSku::GetProductInfo($intOfferElementID);

        if (is_numeric($arElement['ID'])) {
            self::setMinMaxByProduct($arElement['ID'], $intCatalogGroupID, $strMaxPropertyName, $strMinPropertyName);
        }
    }

    public static function setMinMaxByProduct($intProductElementID, $intCatalogGroupID, $strMaxPropertyName = 'MAXIMUM_PRICE', $strMinPropertyName = 'MINIMUM_PRICE')
    {

        $arOffers = \CCatalogSKU::getOffersList(array($intProductElementID));

        $arOffersIds = array_keys($arOffers[$intProductElementID]);

        $arFields = array(
            'CATALOG_GROUP_ID' => $intCatalogGroupID,
            'PRODUCT_ID' => $arOffersIds
        );

        $arMinimum = \CPrice::GetList(array(), $arFields, array('MIN' => 'PRICE'))->Fetch();
        $arMaximum = \CPrice::GetList(array(), $arFields, array('MAX' => 'PRICE'))->Fetch();

        \CIBlockElement::SetPropertyValuesEx($intProductElementID, false, array($strMinPropertyName => $arMinimum['PRICE']));
        \CIBlockElement::SetPropertyValuesEx($intProductElementID, false, array($strMaxPropertyName => $arMaximum['PRICE']));
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