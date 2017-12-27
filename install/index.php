<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (class_exists('dk_helper')) return;


Class dk_helper extends CModule
{
    var $MODULE_ID = 'dk.helper';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_GROUP_RIGHTS = 'N';

    function dk_helper()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = 'Helper';
        $this->MODULE_DESCRIPTION = Loc::getMessage('DESCRIPTION');

        $this->PARTNER_NAME = Loc::getMessage('PARTNER_NAME');
        $this->PARTNER_URI = '';
    }

    function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        return true;
    }

    function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
        return true;
    }
}
