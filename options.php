<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$strModuleID = 'swebs.integration1c';

Loader::includeModule($strModuleID);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $strModuleID . '/lib/CModuleOptions.php');

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $strModuleID . '/options.php');

$isShowRightsTab = false;

$arTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('DK_TAB_NAME'),
        'ICON' => '',
        'TITLE' => Loc::getMessage('DK_TAB_TITLE')
    )
);

$arGroups = array(
    'MAIN' => array('TITLE' => Loc::getMessage('DK_GROUP_MAIN_TITLE'), 'TAB' => 0)
);

$arOptions = array(
    'SELECTOR' => array(
        'GROUP' => 'MAIN',
        'TITLE' => 'Селектор:',
        'TYPE' => 'STRING',
        'DEFAULT' => 'sh',
        'SORT' => '10'
    )
);

$obOptions = new CModuleOptions($strModuleID, $arTabs, $arGroups, $arOptions, $isShowRightsTab);
$obOptions->ShowHTML();
