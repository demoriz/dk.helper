<?php

namespace DK\Helper\Sale;

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Sale;
use Bitrix\Sale\Basket;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Fuser;
use DK\Helper;

Loader::includeModule('sale');

class Order
{
    public static function getPropertyValueByCode($obOrder, $strCode)
    {
        $strValue = '';
        $obPropertyCollection = $obOrder->getPropertyCollection();
        foreach ($obPropertyCollection as $obProperty) {
            if ($obProperty->getField('CODE') == $strCode) {
                $strValue = $obProperty->getField('VALUE');
                break;
            }
        }

        return $strValue;
    }

    public static function setPropertyValueByCode($obOrder, $strCode, $strValue)
    {
        $obPropertyCollection = $obOrder->getPropertyCollection();
        foreach ($obPropertyCollection as $obProperty) {
            if ($obProperty->getField('CODE') == $strCode) {
                $obProperty->setValue($strValue);
                break;
            }
        }

        return true;
    }

    public static function getDeliveries($intUserID = NULL)
    {
        if ($intUserID == NULL) {
            $intUserID = Helper\Main\User::getID(true);
        }

        $strSiteId = Context::getCurrent()->getSite();
        $obOrder = Sale\Order::create($strSiteId, $intUserID);

        $obShipment = $obOrder->getShipmentCollection()->createItem();
        $arDelivery = Sale\Delivery\Services\Manager::getRestrictedObjectsList($obShipment);

        return $arDelivery;
    }

    public static function getPaySystems($intUserID = NULL, $intDeliveryID = false)
    {
        if ($intUserID == NULL) {
            $intUserID = Helper\Main\User::getID(true);
        }

        $strSiteId = Context::getCurrent()->getSite();
        $obOrder = Sale\Order::create($strSiteId, $intUserID);

        if (is_numeric($intDeliveryID)) {
            $obShipment = $obOrder->getShipmentCollection()->createItem();
            $obShipment->setField('DELIVERY_ID', $intDeliveryID);
        }

        $obPayment = $obOrder->getPaymentCollection()->createItem();
        $arPayment = Sale\PaySystem\Manager::getListWithRestrictions($obPayment);

        return $arPayment;
    }

    public static function byOneClick($intUserID = NULL, $arProperties)
    {
        if (empty($arProperties['PRODUCT_ID'])) {
            throw new SystemException('Missing required "PRODUCT_ID"');
        }
        if (empty($arProperties['DELIVERY_ID'])) {
            throw new SystemException('Missing required "DELIVERY_ID"');
        }
        if (empty($arProperties['PAYMENT_ID'])) {
            throw new SystemException('Missing required "PAYMENT_ID"');
        }
        if ($intUserID == NULL) {
            $intUserID = Helper\Main\User::getID(true);
        }
        if (empty($arProperties['PERSONAL_ID'])) {
            $arProperties['PERSONAL_ID'] = 1;
        }

        $strSiteID = Context::getCurrent()->getSite();

        $intQuantity = 1;

        $obBasket = Basket::loadItemsForFUser(Fuser::getId(), $strSiteID);

        Helper\Sale\Basket::clean($obBasket);

        if ($obItem = $obBasket->getExistsItem('catalog', $arProperties['PRODUCT_ID'])) {
            $obItem->setField('QUANTITY', $obItem->getQuantity() + $intQuantity);
        } else {
            $strProductXmlId = Helper\Iblock\Element::getFieldsByID($arProperties['PRODUCT_ID'], 'XML_ID');
            $intSectionID = Helper\Iblock\Element::getFieldsByID($arProperties['PRODUCT_ID'], 'IBLOCK_SECTION_ID');
            $strCatalogXmlId = '';
            if (is_numeric($intSectionID)) {
                $strCatalogXmlId = Helper\Iblock\Section::getFieldsByID($intSectionID, 'XML_ID');
            }
            $obItem = $obBasket->createItem('catalog', $arProperties['PRODUCT_ID']);
            $obItem->setFields(array(
                'QUANTITY' => $intQuantity,
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'LID' => $strSiteID,
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                'PRODUCT_XML_ID' => $strProductXmlId,
                'CATALOG_XML_ID' => $strCatalogXmlId
            ));
        }
        $obBasket->save();

        unset($arProperties['PRODUCT_ID']);

        return self::simpleOrder($intUserID, $arProperties);
    }

    public static function simpleOrder($intUserID = NULL, $arProperties)
    {
        if (empty($arProperties['DELIVERY_ID'])) {
            throw new SystemException('Missing required "DELIVERY_ID"');
        }
        if (empty($arProperties['PAYMENT_ID'])) {
            throw new SystemException('Missing required "PAYMENT_ID"');
        }
        if ($intUserID == NULL) {
            $intUserID = Helper\Main\User::getID(true);
        }
        if (empty($arProperties['PERSONAL_ID'])) {
            $arProperties['PERSONAL_ID'] = 1;
        }

        $strSiteID = Context::getCurrent()->getSite();

        // Coupon
        DiscountCouponsManager::init();
        if (!empty($arProperties['COUPON'])) {
            DiscountCouponsManager::add($arProperties['COUPON']);
        }

        $obOrder = Sale\Order::create($strSiteID, $intUserID);
        $obOrder->setPersonTypeId($arProperties['PERSONAL_ID']);

        $obBasket = Basket::loadItemsForFUser(Fuser::getId(), $strSiteID);
        $obOrder->setBasket($obBasket);

        // Shipment
        $shipmentCollection = $obOrder->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $shipment->setFields(array(
            'DELIVERY_ID' => $arProperties['DELIVERY_ID']
        ));

        $shipmentItemCollection = $shipment->getShipmentItemCollection();

        foreach ($obOrder->getBasket() as $item) {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }

        $shipmentCollection->calculateDelivery();

        // Payment
        $arPaymentFields = \CSalePaySystem::GetByID($arProperties['PAYMENT_ID']);
        $paymentCollection = $obOrder->getPaymentCollection();
        $extPayment = $paymentCollection->createItem();
        $extPayment->setFields(array(
            'PAY_SYSTEM_ID' => $arProperties['PAYMENT_ID'],
            'PAY_SYSTEM_NAME' => $arPaymentFields['NAME'],
            'SUM' => $obOrder->getPrice()
        ));

        $obOrder->doFinalAction(true);

        if (is_array($arProperties['ORDER_FIELDS'])) {
            foreach ($arProperties['ORDER_FIELDS'] as $strCode => $strValue) {
                $obOrder->setField($strCode, $strValue);
            }
        }

        if (is_array($arProperties['ORDER_PROPERTIES'])) {
            foreach ($arProperties['ORDER_PROPERTIES'] as $strCode => $strValue) {
                self::setPropertyValueByCode($obOrder, $strCode, $strValue);
            }
        }

        $obRes = $obOrder->save();

        if (!$obRes->isSuccess()) {
            throw new SystemException($obRes->getErrors());
        }

        return $obOrder->GetId();
    }
}
