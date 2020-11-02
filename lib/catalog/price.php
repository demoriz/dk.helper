<?php

namespace DK\Helper\Catalog;

use Bitrix\Main\Loader;
use Bitrix\Catalog\GroupTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;

Loader::includeModule('iblock');
Loader::includeModule('catalog');

/**
 * Class Price
 * @package DK\Helper\Catalog
 */
class Price
{
    /**
     * Устанавливаем минимальную и максимальную цены по предложению.
     *
     * @param $intOfferElementID
     * @param $intCatalogGroupID
     * @param string $strMaxPropertyName
     * @param string $strMinPropertyName
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ObjectNotFoundException
     */
    public static function setMinMaxByOffer($intOfferElementID, $intCatalogGroupID, $strMaxPropertyName = 'MAXIMUM_PRICE', $strMinPropertyName = 'MINIMUM_PRICE')
    {

        $arElement = \CCatalogSku::GetProductInfo($intOfferElementID);

        if (is_numeric($arElement['ID'])) {
            self::setMinMaxByProduct($arElement['ID'], $intCatalogGroupID, $strMaxPropertyName, $strMinPropertyName);
        }
    }

    /**
     * Устанавливаем минимальную и максимальную цены по товару.
     *
     * @param $intProductElementID
     * @param $intCatalogGroupID
     * @param string $strMaxPropertyName
     * @param string $strMinPropertyName
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ObjectNotFoundException
     */
    public static function setMinMaxByProduct($intProductElementID, $intCatalogGroupID, $strMaxPropertyName = 'MAXIMUM_PRICE', $strMinPropertyName = 'MINIMUM_PRICE')
    {

        $arOffers = \CCatalogSKU::getOffersList(array($intProductElementID));

        $arOffersIds = array_keys($arOffers[$intProductElementID]);

        $arFields = array(
            'order' => array(
                'PRICE' => 'asc'
            ),
            'filter' => array(
                'PRODUCT_ID' => $arOffersIds,
                'CATALOG_GROUP_ID' => $intCatalogGroupID
            )
        );
        $dbPrice = \Bitrix\Catalog\Model\Price::getList($arFields);
        $arMinimum = $dbPrice->fetch();

        $arFields['order']['PRICE'] = 'desc';
        $dbPrice = \Bitrix\Catalog\Model\Price::getList($arFields);
        $arMaximum = $dbPrice->fetch();


        \CIBlockElement::SetPropertyValuesEx($intProductElementID, false, array($strMinPropertyName => $arMinimum['PRICE']));
        \CIBlockElement::SetPropertyValuesEx($intProductElementID, false, array($strMaxPropertyName => $arMaximum['PRICE']));
    }

    /**
     * Получаем цену со скидкой.
     *
     * @param $intProductElementID
     * @param $intCatalogGroupID
     * @return array|false|float|int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ObjectNotFoundException
     */
    public static function getDiscountPrice($intProductElementID, $intCatalogGroupID)
    {
        $flPrice = 0;

        $arFields = array(
            'order' => array(
                'PRICE' => 'desc'
            ),
            'filter' => array(
                'PRODUCT_ID' => $intProductElementID,
                'CATALOG_GROUP_ID' => $intCatalogGroupID
            )
        );
        $dbPrice = \Bitrix\Catalog\Model\Price::getList($arFields);
        if ($arPrice = $dbPrice->fetch()) {
            $arDiscount = \CCatalogDiscount::GetDiscountByProduct($intProductElementID);
            $flPrice = \CCatalogProduct::CountPriceWithDiscount($arPrice['PRICE'], $arPrice['CURRENCY'], $arDiscount);
        }

        return $flPrice;
    }

    /**
     * Добавляем или обновляем цену.
     *
     * @param $intProductID
     * @param $intPriceTypeID
     * @param $floatPrice
     * @param string $strCurrency
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ObjectNotFoundException
     */
    static public function add($intProductID, $intPriceTypeID, $floatPrice, $strCurrency = 'RUB')
    {
        $arFieldsPrice = array(
            'PRODUCT_ID' => $intProductID,
            'PRICE' => $floatPrice,
            'CATALOG_GROUP_ID' => $intPriceTypeID,
            'CURRENCY' => $strCurrency
        );

        $dbPrice = \Bitrix\Catalog\Model\Price::getList(array(
            'filter' => array(
                'PRODUCT_ID' => $intProductID,
                'CATALOG_GROUP_ID' => $intPriceTypeID
            )));


        if ($arPrice = $dbPrice->fetch()) {
            $result = \Bitrix\Catalog\Model\Price::update($arPrice['ID'], $arFieldsPrice);
            if (!$result->isSuccess()) throw new SystemException($result->getErrorMessages());

        } else {
            $result = \Bitrix\Catalog\Model\Price::add($arFieldsPrice);
            if (!$result->isSuccess()) throw new SystemException($result->getErrorMessages());
        }
    }

    /**
     * Возвращаем базовую цену.
     *
     * @return array|false|mixed
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getBasePrice()
    {
        $dbGroup = GroupTable::getList(array(
            'filter' => array('BASE' => 'Y'),
            'select' => array('*'),
            'cache' => array('ttl' => 3600),
        ));

        return $dbGroup->fetch();
    }
}