<?php
/**
 * Created by PhpStorm.
 * @author Karikh Dmitriy <demoriz@gmail.com>
 * @date 27.11.2020
 */

namespace DK\Helper\Sale;


use Bitrix\Sale\Fuser;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Discount\Gift;
use Bitrix\Main\SystemException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\NotImplementedException;
use Bitrix\Sale\Compatible\DiscountCompatibility;

Loader::includeModule('sale');
Loader::includeModule('catalog');


/**
 * Class Gifts
 * @package DK\Helper\Sale
 */
class Gifts
{
    /**
     * Возвращает массив id всех подарков для товара с учётом его предложений если такие есть.
     * @param int $intProductId
     * @return array
     * @throws ArgumentException
     * @throws ArgumentTypeException
     * @throws NotImplementedException
     * @throws SystemException
     */
    public static function getGifts(int $intProductId)
    {
        $arGifts = array();

        $arExistOffers = \CCatalogSKU::getExistOffers(array($intProductId));
        if ($arExistOffers[$intProductId]) {
            $arOffers = \CCatalogSKU::getOffersList(array($intProductId));
            $arOffersIds = array_keys($arOffers[$intProductId]);

            foreach ($arOffersIds as $intId) {
                $arOfferGifts = self::getByProduct($intId);

                if (empty($arOfferGifts)) continue;

                if (empty($arGifts)) {
                    $arGifts = $arOfferGifts;

                } else {
                    $arGifts = array_intersect($arGifts, $arOfferGifts);
                }
            }

        } else {
            $arGifts = self::getByProduct($intProductId);
        }

        return $arGifts;
    }


    /**
     * Возвращает массив id всех доступных подарков для товара (торгового предложения)
     *
     * @param int $intProductId - идентификатор товара
     * @return array - массив с id подарков для товара
     * @throws ArgumentException
     * @throws ArgumentTypeException
     * @throws NotImplementedException
     * @throws SystemException
     */
    private static function getByProduct(int $intProductId)
    {
        $arGiftProductIds = array();

        DiscountCompatibility::stopUsageCompatible();

        $giftManager = Gift\Manager::getInstance();

        $potentialBuy = array(
            'ID' => $intProductId,
            'MODULE' => 'catalog',
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
            'QUANTITY' => 1,
        );

        $basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);

        $basketPseudo = $basket->copy();

        foreach ($basketPseudo as $basketItem) {
            $basketItem->delete();
        }

        $collections = $giftManager->getCollectionsByProduct($basketPseudo, $potentialBuy);

        foreach ($collections as $collection) {
            /** @var Gift\Gift $gift */
            foreach ($collection as $gift) {
                $arGiftProductIds[] = $gift->getProductId();
            }
        }

        DiscountCompatibility::revertUsageCompatible();

        return $arGiftProductIds;
    }
}
