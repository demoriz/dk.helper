<?php

namespace DK\Helper\Main;

use \Bitrix\Main\Web\HttpClient;
use \Bitrix\Main\Application;
use \Bitrix\Main\IO;
use \Bitrix\Main\Config\Option;

class System
{
    private const MODULE_ID = 'dk.helper';

    public static function update()
    {
        $http = new HttpClient();

        $strBranch = Option::get(self::MODULE_ID, 'BRANCH');
        $strModulePath = dirname(__FILE__) . '/../../';

        $strFileUrl = 'https://github.com/demoriz/dk.helper/archive/' . $strBranch . '.tar.gz';
        $strFilePath = '/upload/dk_helper.tar.gz';
        $strDirPath = '/upload/';

        $strFilePath = Application::getDocumentRoot() . $strFilePath;
        $strDirPath = Application::getDocumentRoot() . $strDirPath;

        $file = new IO\File($strFilePath);
        if ($file->isExists()) {
            $file->delete();
        }
        $dir = new IO\Directory($strDirPath . '/dk.helper-' . $strBranch . '/');
        if ($dir->isExists()) {
            $dir->delete();
        }
        $dir = new IO\Directory($strDirPath . '/dk_helper/');
        if ($dir->isExists()) {
            $dir->delete();
        }

        $isResult = $http->download($strFileUrl, $strFilePath);

        // скачалось
        if ($isResult) {
            $archive = \CBXArchive::GetArchive($strFilePath, 'TAR.GZ');
            $result = $archive->Unpack($strDirPath);

            // обработка архива
            if ($result === true) {

                $dir = new IO\Directory(Application::getDocumentRoot() . '/upload/dk.helper-' . $strBranch . '/');

                // распаковалось
                if ($dir->isExists()) {
                    // удаляем старый модуль
                    $old = new IO\Directory($strModulePath);
                    if ($old->isExists()) {
                        $old->delete();
                    }

                    // копируем новый
                    $result = $dir->rename($strModulePath);

                    if ($result) {
                        $file = new IO\File($strModulePath . 'install/index.php');
                        if ($file->isExists()) {
                            include_once($file->getPath());

                            $module = new \dk_helper();
                            if ($module->IsInstalled()) {
                                $module->DoUninstall();
                            }
                            $module->DoInstall();
                        }

                        $isResult = true;
                    }
                }
            }
        }

        return $isResult;
    }

    public static function version()
    {
        $strLatestVersion = file_get_contents(__DIR__ . '/../../install/version.php');
        preg_match("/\"VERSION\" => \"(.*)\"/", $strLatestVersion, $arLatestMatches);

        $strVersion = file_get_contents('https://raw.githubusercontent.com/demoriz/dk.helper/' . Option::get(self::MODULE_ID, 'BRANCH') . '/install/version.php');
        preg_match("/\"VERSION\" => \"(.*)\"/", $strVersion, $arMatches);

        $strNewVersion = '';

        if ($arMatches[1] != $arLatestMatches[1]) {
            $strNewVersion = $arMatches[1];
        }

        return $strNewVersion;
    }
}