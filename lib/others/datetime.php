<?php

namespace DK\Helper\Others;

use Bitrix\Main\Type;
use Bitrix\Main\ObjectException;

/**
 * Class DateTime
 * @package DK\Helper\Others
 */
class DateTime
{
    /**
     * @param Type\DateTime $obFirstData
     * @param Type\DateTime $obSecondData
     * @return array
     */
    static public function getInterval(Type\DateTime $obFirstData, Type\DateTime $obSecondData)
    {
        $intDiff = $obFirstData->getTimestamp() - $obSecondData->getTimestamp();

        $arTimes = array();

        if ($intDiff > 0) { // считать нули в значениях
            $bCountZero = false;

            // количество секунд в году не учитывает високосный год
            // поэтому функция считает что в году 365 дней
            // секунд в минуте|часе|сутках|году
            $arPeriods = array(60, 3600, 86400, 31536000);

            for ($i = 3; $i >= 0; $i--) {
                $intPeriod = floor($intDiff / $arPeriods[$i]);
                if (($intPeriod > 0) || ($intPeriod == 0 && $bCountZero)) {
                    $arTimes[$i + 1] = $intPeriod;
                    $intDiff -= $intPeriod * $arPeriods[$i];

                    $bCountZero = true;
                }
            }

            $arTimes[0] = $intDiff;
        }

        return $arTimes;
    }

    /**
     * @param $strDate
     * @param $strFormat1
     * @param $strFormat2
     * @return string
     * @throws ObjectException
     */
    static public function reFormatDate($strDate, $strFormat1, $strFormat2)
    {
        $date = new Type\DateTime($strDate, $strFormat1);

        $strDate = $date->format($strFormat2);

        return $strDate;
    }

    /**
     * @param Type\DateTime $date
     * @return bool
     * @throws ObjectException
     */
    static public function isToday(Type\DateTime $date)
    {
        $isToday = false;

        $strDate = $date->format('Y-m-d');
        $date = new Type\DateTime();
        $strToday = $date->format('Y-m-d');

        if ($strDate == $strToday) {
            $isToday = true;
        }

        return $isToday;
    }

    /**
     * @param Type\DateTime $date
     * @return bool
     * @throws ObjectException
     */
    static public function isTomorrow(Type\DateTime $date)
    {
        $isTomorrow = false;

        $strDate = $date->format('Y-m-d');
        $date = new Type\DateTime();
        $date->add('1 day');
        $strTomorrow = $date->format('Y-m-d');

        if ($strDate == $strTomorrow) {
            $isTomorrow = true;
        }

        return $isTomorrow;
    }

    /**
     * @param Type\DateTime $date
     * @return bool
     * @throws ObjectException
     */
    static public function isYesterday(Type\DateTime $date)
    {
        $isYesterday = false;

        $strDate = $date->format('Y-m-d');
        $date = new Type\DateTime();
        $date->add('-1 day');
        $strYesterday = $date->format('Y-m-d');

        if ($strDate == $strYesterday) {
            $isYesterday = true;
        }

        return $isYesterday;
    }

    static public function getHowMuchIsLeft($intTimestamp)
    {
        $datetime1 = date_create(FormatDate('Y-m-d H:i', $intTimestamp));
        $datetime2 = date_create('now', new \DateTimeZone('Europe/Moscow'));
        $interval = date_diff($datetime1, $datetime2);

        $strData = $interval->format('%y-%m-%d-%h-%i');
        $arData = explode('-', $strData);

        $strTime = '';
        if ($arData[0] > 0) {
            $strTime .= Strings::declension($arData[0], array('год', 'года', 'лет'), true);
        }
        if ($arData[1] > 0) {
            $strTime .= ' ' . Strings::declension($arData[1], array('месяц', 'месяца', 'месяцев'), true);
        }
        if ($arData[2] > 0) {
            $strTime .= ' ' . Strings::declension($arData[2], array('день', 'дня', 'дней'), true);
        }
        if ($arData[3] > 0) {
            $strTime .= ' ' . Strings::declension($arData[3], array('час', 'часа', 'часов'), true);
        }
        if ($arData[4] > 0) {
            $strTime .= ' ' . Strings::declension($arData[4], array('минута', 'минуты', 'минут'), true);
        }

        return $strTime;
    }
}