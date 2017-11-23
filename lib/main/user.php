<?php

namespace DK\Helper\Main;

use Bitrix\Main\SystemException;

class User
{
    public static function getID($isAllowAnonymous = false)
    {
        global $USER;

        $intUserID = $USER->GetID();

        if ($intUserID == NULL && $isAllowAnonymous) {
            $intUserID = \CSaleUser::GetAnonymousUserID();
        }

        return $intUserID;
    }

    public static function getByLogin($strLogin, $arParams = array())
    {
        $dbUser = \CUser::GetByLogin($strLogin);
        if ($arUser = $dbUser->Fetch()) {
            // Пользователь существует
            return $arUser;
        }
        if (!empty($arParams)) {
            // Пользователь новый
            if (empty($arParams['LOGIN'])) {
                $arParams['LOGIN'] = $strLogin;
            }
            if (empty($arParams['PASSWORD'])) {
                $arParams['PASSWORD'] = uniqid();
                $arParams['CONFIRM_PASSWORD'] = $arParams['PASSWORD'];
            }
            $obUser = new \CUser;;
            $intUserID = $obUser->Add($arParams);

            if (intval($intUserID)) {
                $arParams['ID'] = $intUserID;
            } else {
                throw new SystemException($obUser->LAST_ERROR);
            }

            return $arParams;
        }

        return false;
    }
}