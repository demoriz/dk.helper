<?php

namespace DK\Helper\Main;

class System
{
    public static function update()
    {
        $strUrl = 'https://github.com/demoriz/dk.helper/archive/master.zip';
        $strLocalPath = $_SERVER["DOCUMENT_ROOT"] . '/upload/master.zip';
        $strExtractPath = $_SERVER["DOCUMENT_ROOT"] . '/upload/';

        $httpClient = new \Bitrix\Main\Web\HttpClient();
        $httpClient->download($strUrl, $strLocalPath);

        $zip = new \ZipArchive;
        $zip->open($strLocalPath);
        $zip->extractTo($strExtractPath);
    }

    public function removeSpaces()
    {
    }
}