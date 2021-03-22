<?php

namespace DK\Helper\Others;

use Bitrix\Main\Web;
use Bitrix\Main\Context;
use Bitrix\Main\Application;

class Cookie
{
    public static function getCookie($strName = false)
    {
        $obRequest = Context::getCurrent()->getRequest();
        if ($strName === false) {
            return $obRequest->getCookieList();
        }

        return $obRequest->getCookie($strName);
    }

    public static function setCookie($strName, $strValue, $isAjax = false, $strDomain = '')
    {
        $obContext = Context::getCurrent();
        $obCookie = new Web\Cookie($strName, $strValue);
        if (!empty($strDomain)) {
            $obCookie->setDomain($strDomain);
        }
        $obContext->getResponse()->addCookie($obCookie);

        if ($isAjax) Application::getInstance()->getContext()->getResponse()->flush();
    }
}