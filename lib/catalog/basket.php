<?php
/**
 * Created by PhpStorm.
 * @author Karikh Dmitriy <demoriz@gmail.com>
 * @date 30.10.2020
 */

namespace DK\Helper\Catalog;

use Bitrix\Sale;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ObjectNotFoundException;
use DK\Helper\Iblock\Element as DkElement;
use DK\Helper\Iblock\Section as DkSection;
use Bitrix\Main\ArgumentOutOfRangeException;

Loader::includeModule('sale');
Loader::includeModule('iblock');


/**
 * Class Basket
 * @package Siart\Project\Helper
 */
class Basket
{
    /**
     * @var Sale\Basket $basket
     */
    private $basket;
    private $arBasketItems = array();

    /**
     * Basket constructor.
     */
    public function __construct()
    {
        $this->basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Context::getCurrent()->getSite());
    }

    /**
     * Очищаем корзину полностью.
     *
     * @throws ArgumentException
     * @throws ArgumentNullException
     */
    public function clean()
    {
        foreach ($this->basket as $item) $item->delete();
        $this->basket->save();
        $this->arBasketItems = array();
    }

    /**
     * Добавляем (удаляем) товар в корзину.
     *
     * @param $intProductID
     * @param int $intQuantity
     * @param false $isXmlId
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ObjectNotFoundException
     * @throws SystemException
     */
    public function add($intProductID, $intQuantity = 1, $isXmlId = false)
    {
        if (!is_numeric($intQuantity)) $intQuantity = 1;
        $intQuantity = (int)$intQuantity;

        if (!is_bool($isXmlId)) {
            $isXmlId = false;
        }

        if ($item = $this->basket->getExistsItem('catalog', $intProductID)) {
            $intQuantity = $item->getQuantity() + $intQuantity;
            if ($intQuantity < 1) $item->delete();
            else $item->setField('QUANTITY', $intQuantity);

        } else {
            $item = $this->basket->createItem('catalog', $intProductID);
            $arFields = array(
                'QUANTITY' => $intQuantity,
                'CURRENCY' => CurrencyManager::getBaseCurrency(),
                'LID' => Context::getCurrent()->getSite(),
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            );

            if ($isXmlId) {
                $strProductXmlId = DkElement::getFieldsByID($intProductID, 'XML_ID');
                $intSectionID = DkElement::getFieldsByID($intProductID, 'IBLOCK_SECTION_ID');

                $arFields['PRODUCT_XML_ID'] = $strProductXmlId;

                if (is_numeric($intSectionID) && $intSectionID > 0) {
                    $strCatalogXmlId = DkSection::getFieldsByID($intSectionID, 'XML_ID');
                    $arFields['CATALOG_XML_ID'] = $strCatalogXmlId;
                }
            }
            $item->setFields($arFields);
        }
        $this->basket->save();
        $this->getBasketItems(true);
    }

    /**
     * Удаляем товар из корзины по id.
     *
     * @param $intItemID
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectNotFoundException
     */
    public function delete($intItemID)
    {
        $this->basket->getItemById($intItemID)->delete();
        $this->basket->save();
        $this->getBasketItems(true);
    }

    /**
     * Удаляем товар из корзины по id товара.
     *
     * @param $intProductID
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ObjectNotFoundException
     */
    public function deleteByProductId($intProductID)
    {
        $this->basket->getExistsItem('catalog', $intProductID)->delete();
        $this->basket->save();
        $this->getBasketItems(true);
    }

    /**
     * Получаем количество товаров в корзине.
     *
     * @param false $isTotal
     * @return int
     */
    public function count($isTotal = false)
    {
        $intCount = 0;
        foreach ($this->basket as $basketItem) {
            if ($isTotal) $intCount += $basketItem->getQuantity();
            else $intCount++;
        }

        return (int)$intCount;
    }

    /**
     * Возвращает количество переданного товара в корзине.
     *
     * @param $intProductID
     * @return int
     */
    public function getInBasket($intProductID)
    {
        $this->getBasketItems();

        return (int)$this->arBasketItems[$intProductID]['QUANTITY'];
    }

    /**
     * Получаем инфорацию о товарах в корзине.
     *
     * @param false $isRefresh
     */
    public function getBasketItems($isRefresh = false)
    {
        if ($isRefresh !== true) $isRefresh = false;

        if (empty($this->arBasketItems) || $isRefresh) {
            $this->arBasketItems = array();
            foreach ($this->basket as $item) {
                $this->arBasketItems[$item->getProductId()] = array(
                    'NAME' => $item->getField('NAME'),
                    'QUANTITY' => $item->getQuantity()
                );
            }
        }
    }
}
