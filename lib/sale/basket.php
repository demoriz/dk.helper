<?php

namespace DK\Helper\Sale;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Sale;
use Bitrix\Sale\DiscountCouponsManager;

Loader::includeModule('sale');

class Basket
{
    public static function clean(Sale\Basket $obBasket)
    {
        if (empty($obBasket)) {
            throw new SystemException('Missing required "$obBasket"');
        }
        foreach ($obBasket as $obItem) {
            $obItem->delete();
            $obBasket->save();
        }
    }

    public static function add($intProductID, $intQuantity = 1, $isXmlId = false)
    {
        if (!is_numeric($intQuantity)) {
            $intQuantity = 1;
        }

        if (!is_bool($isXmlId)) {
            $isXmlId = false;
        }

        $obContext = Context::getCurrent();

        $obBasket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), $obContext->getSite());
        if ($obItem = $obBasket->getExistsItem('catalog', $intProductID)) {
            $obItem->setField('QUANTITY', $obItem->getQuantity() + $intQuantity);
        } else {
            $obItem = $obBasket->createItem('catalog', $intProductID);
            $arFields = array(
                'QUANTITY' => $intQuantity,
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'LID' => $obContext->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            );

            if ($isXmlId) {
                $strProductXmlId = DK\Helper\Iblock\Element::getFieldsByID($intProductID, 'XML_ID');
                $intSectionID = DK\Helper\Iblock\Element::getFieldsByID($intProductID, 'IBLOCK_SECTION_ID');

                $arFields['PRODUCT_XML_ID'] = $strProductXmlId;

                if (is_numeric($intSectionID) && $intSectionID > 0) {
                    $strCatalogXmlId = DK\Helper\Iblock\Section::getFieldsByID($intSectionID, 'XML_ID');
                    $arFields['CATALOG_XML_ID'] = $strCatalogXmlId;
                }
            }
            $obItem->setFields($arFields);
        }
        $obBasket->save();
    }

    public static function delete($intItemID)
    {
        $obContext = Context::getCurrent();
        $obBasket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), $obContext->getSite());
        $obBasket->getItemById($intItemID)->delete();
        $obBasket->save();
    }

    public static function deleteByProductId($intProductID)
    {
        $obContext = Context::getCurrent();
        $obBasket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), $obContext->getSite());
        $obBasket->getExistsItem('catalog', $intProductID)->delete();
        $obBasket->save();
    }

    public static function count()
    {
        $obContext = Context::getCurrent();

        $obBasket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), $obContext->getSite());

        $intCount = 0;
        foreach ($obBasket as $obBasketItem) {
            $intCount += $obBasketItem->getQuantity();
        }

        return $intCount;
    }

    public static function getDiscountSum($strCoupon = '')
    {
        global $USER;

        $obContext = Context::getCurrent();
        DiscountCouponsManager::clear();
        $obBasket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Context::getCurrent()->getSite());

        if (!empty($strCoupon)) {
            DiscountCouponsManager::add($strCoupon);
            \CSaleBasket::UpdateBasketPrices(Sale\Fuser::getId(), $obContext->getSite());
        }

        $arBasketItems = array();

        foreach ($obBasket as $obBasketItem) {
            $arBasketItems[] = array(
                'PRODUCT_ID' => $obBasketItem->getProductId(),
                'PRODUCT_PRICE_ID' => $obBasketItem->getField('PRODUCT_PRICE_ID'),
                'PRICE' => $obBasketItem->getPrice(),
                'BASE_PRICE' => $obBasketItem->getBasePrice(),
                'QUANTITY' => $obBasketItem->getQuantity(),
                'LID' => $obBasketItem->getField('LID'),
                'MODULE' => $obBasketItem->getField('MODULE')
            );
        }

        $arOrder = array(
            'SITE_ID' => $obContext->getSite(),
            'USER_ID' => $USER->GetID(),
            'ORDER_PRICE' => $obBasket->getPrice(),
            'ORDER_WEIGHT' => $obBasket->getWeight(),
            'BASKET_ITEMS' => $arBasketItems
        );

        $arOptions = array(
            'COUNT_DISCOUNT_4_ALL_QUANTITY' => 'Y',
        );

        $arErrors = array();

        \CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

        $arResult = array(
            'TOTAL_PRICE' => $arOrder['ORDER_PRICE'],
            'BASKET_ITEMS' => array()
        );

        foreach ($arOrder['BASKET_ITEMS'] as $arItem) {
            $arResult['BASKET_ITEMS'][$arItem['PRODUCT_ID']] = $arItem['PRICE'];
        }

        return $arResult;
    }
}
