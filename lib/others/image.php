<?php

namespace DK\Helper\Others;

class Image
{
    public static function resize($intFileID, $intWidth = 999999, $intHeight = 999999)
    {
        if (!is_numeric($intWidth)) {
            $intWidth = 999999;
        }
        if (!is_numeric($intHeight)) {
            $intHeight = 999999;
        }

        $arSize = array(
            'width' => $intWidth,
            'height' => $intHeight
        );

        $arImage = \CFile::ResizeImageGet($intFileID, $arSize, BX_RESIZE_IMAGE_PROPORTIONAL, true);
        $arImage = array_change_key_case($arImage, CASE_UPPER);

        return $arImage;
    }
}