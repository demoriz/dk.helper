<?php

namespace DK\Helper\Main;

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

        echo '<pre style="background:#363636; color:#fff; padding: 7px; border-radius: 5px; font-size: 9px; display: block; z-index: 999999999;">';

        if (is_array($obData) || is_object($obData)) {
            echo htmlspecialcharsbx(print_r($obData, true));
        } else {
            echo htmlspecialcharsbx($obData);
        }

        echo '</pre>';
    }

    public static function dump($obData, $strFlag = false)
    {
        global $USER;

        if ($strFlag === false) {
            if (!$USER->IsAdmin()) {
                return false;
            }
        }

        echo '<pre style="background:#363636; color:#fff; padding: 7px; border-radius: 5px; font-size: 9px; display: block; z-index: 999999999;">';

        ob_start();
        var_dump($obData);
        $strDump = ob_get_contents();
        ob_end_clean();


        echo $strDump . '</pre>';
    }
}