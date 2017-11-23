<?php

namespace DK\Helper\Others;

use Bitrix\Main\Type;

class DateTime
{
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
}