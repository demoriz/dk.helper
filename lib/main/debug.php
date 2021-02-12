<?php

namespace DK\Helper\Main;

use Bitrix\Main\Diag\Debug as BxDebug;
use Bitrix\Main\Type\DateTime;

class Debug
{
    public static function show($obData, $strFlag = false)
    {
        global $USER;

        if ($strFlag === false) {
            if (!$USER->IsAdmin()) {
                return false;
            }
        }

        $arTrace = debug_backtrace();
        $arFileLines = file($arTrace[0]['file']);
        $strLineData = $arFileLines[$arTrace[0]['line'] - 1];
        preg_match("#show\\((.+)\\);#", $strLineData, $arData);

        $strVarName = '';
        if (!empty($arData[1])) {
            $arParts = explode(',', $arData[1]);
            if (count($arParts) > 1) {
                unset($arParts[count($arParts) - 1]);
            }
            $strVarName = implode(',', $arParts);
        }

        $strFilePath = substr($arTrace[0]['file'], strlen($_SERVER['DOCUMENT_ROOT']));

        echo '<pre style="background:#363636; color:#bababa; padding: 7px; border-radius: 5px; font-size: 12px; display: block; z-index: 999999999; line-height: 16px">';
        echo '<span style="color: #7f7f7f;">';
        echo '<span style="color: #bababa;">Строка:</span>(<span style="color: #6896ba;">' . $arTrace[0]['line'] . '</span>)';
        if (!empty($strVarName)) {
            echo ',&nbsp;<span style="color: #bababa;">Переменная:</span>(<span style="color: #6896ba;">' . $strVarName . '</span>)';
        }
        echo '<br><span style="color: #bababa;">Файл:</span>(<span style="color: #6896ba;">' . $strFilePath . '</span>)';
        echo '</span><br>';
        echo self::dump($obData);
        echo '</pre>';
    }

    private static function dump($data, $intIndent = 1)
    {
        $strRetVal = '';
        $strPrefix = '<span style="color: #7f7f7f;">' . \str_repeat(' |  ', $intIndent) . '</span>';

        if (\is_numeric($data)) {
            $strRetVal .= '<span style="color: #cb7832;">Number</span>: <span style="color: #6896ba;">' . $data . '</span>';

        } elseif (\is_string($data)) {
            $strRetVal .= '<span style="color: #cb7832;">String</span>: <span style="color: #6a8759;">' . "'" . htmlentities($data) . "'" . '</span>';

        } elseif (\is_null($data)) {
            $strRetVal .= '<span style="color: #bababa;">NULL</span>';

        } elseif ($data === true) {
            $strRetVal .= '<span style="color: #3a7fd2;">TRUE</span>';

        } elseif ($data === false) {
            $strRetVal .= '<span style="color: #e03b8c;">FALSE</span>';

        } elseif (is_array($data)) {
            $strRetVal .= '<span style="color: #cb7832;">Array</span> (<span style="color: #6896ba;">' . count($data) . '</span>)';
            $intIndent++;
            foreach ($data as $key => $value) {
                $strRetVal .= "\n" . $strPrefix;
                if (\is_numeric($key)) {
                    $strRetVal .= '[<span style="color: #6896ba;">' . $key . '</span>] = ';
                } else {
                    $strRetVal .= '[' . $key . '] = ';
                }
                $strRetVal .= self::dump($value, $intIndent);
            }

        } elseif (is_object($data)) {
            $strRetVal .= '<span style="color: #cb7832;">Object</span> (<span style="color: #e0c46c;">' . get_class($data) . '</span>)';
            $intIndent++;
            foreach ($data as $key => $value) {
                //$strRetVal .= "\n$strPrefix $key -> ";
                $strRetVal .= "\n" . $strPrefix . ' <span style="color: #e0c46c;">' . $key . '</span> -> ';
                $strRetVal .= self::dump($value, $intIndent);
            }
        }

        return $strRetVal;
    }


    public static function writeLog($strFile, $intLine, $strMessage, $strFileName = '__var.log')
    {
        $date = new DateTime();
        $strName = $strFile . ' [' . $intLine . ']';

        $strMessage .= ' (' . $date->getTimestamp() . ' s)';

        BxDebug::writeToFile($strMessage, $strName, $strFileName);
    }
}