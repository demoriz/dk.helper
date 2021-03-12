<?php

namespace DK\Helper\Others;

class Strings
{
    public static function getStringOfNum($intNum)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('копейка', 'копейки', 'копеек', 1),
            array('рубль', 'рубля', 'рублей', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($intNum)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = self::morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . self::morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop

        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    private static function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;

        return $f5;
    }

    public static function declension($intCount, $arVariants, $bIncludeNumber = false)
    {
        $strWord = '';

        if ($intCount > 20) {
            $intLastNum = substr($intCount, -1);
        } else {
            $intLastNum = $intCount;
        }

        if ($intLastNum == 1) {
            $strWord = $arVariants[0];
        } elseif ((($intLastNum > 1) && ($intLastNum < 5)) || $intLastNum == 3) {
            $strWord = $arVariants[1];
        } elseif ((($intLastNum > 4) && ($intLastNum <= 21)) || $intLastNum == 0) {
            $strWord = $arVariants[2];
        }

        if ($bIncludeNumber) {
            $strWord = $intCount . ' ' . $strWord;
        }

        return $strWord;
    }

    /**
     * @param $str
     * @param string $encoding
     * @return string
     */
    public function upperFirst($str, $encoding = 'UTF8')
    {
        return
            mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
            mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding));
    }

    /**
     * ГОСТ 7.79-2000 схема Б
     *
     * @param $strContent
     * @param bool $isDirection
     * @return string
     */
    public function translit($strContent, $isDirection = true)
    {
        $arRus = array(
            array('Щ', 'щ', ' '),
            array('ё', 'ж', 'ч', 'ш', 'ъ', 'ы', 'э', 'ю', 'я'),
            array('Ё', 'Ж', 'Ч', 'Ш', 'Ы', 'Э', 'Ю', 'Я', 'Ъ'),
            array('а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ь'),
            array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ь')
        );
        $arLat = array(
            array('Shh', 'shh', '_'),
            array('yo', 'zh', 'ch', 'sh', '``', 'y`', 'e`', 'yu', 'ya'),
            array('Yo', 'Zh', 'Ch', 'Sh', 'Y`', 'E`', 'Yu', 'Ya', '``'),
            array('a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', '`'),
            array('A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', '`')
        );

        for ($i = 0; $i < count($arRus); ++$i) {
            if ($isDirection) {
                $strContent = str_replace($arRus[$i], $arLat[$i], $strContent);

            } else {
                $strContent = str_replace($arLat[$i], $arRus[$i], $strContent);
            }
        }

        return $strContent;
    }
}