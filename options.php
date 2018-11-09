<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

$strModuleID = 'dk.helper';

Loader::includeModule($strModuleID);

require_once(__DIR__ . '/lib/CModuleOptions.php');

$isShowRightsTab = false;
$request = Application::getInstance()->getContext()->getRequest();

$arTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('DK_HELPER_TAB_NAME'),
        'ICON' => '',
        'TITLE' => Loc::getMessage('DK_HELPER_TAB_TITLE')
    )
);

$arGroups = array(
    'MAIN' => array('TITLE' => Loc::getMessage('DK_HELPER_GROUP_MAIN_TITLE'), 'TAB' => 0)
);

$arOptions = array(
    'BRANCH' => array(
        'GROUP' => 'MAIN',
        'TITLE' => Loc::getMessage('DK_HELPER_BRANCH_TITLE'),
        'TYPE' => 'SELECT',
        'VALUES' => array(
            'REFERENCE_ID' => array(
                'master',
                'dev'
            ),
            'REFERENCE' => array(
                'master',
                'dev'
            )
        ),
        'DEFAULT' => 'master',
        'SORT' => '10'
    )
);

$strVersion = \DK\Helper\Main\System::version();
if (!empty($strVersion)) {
    $strAdditionalHTML = '<div>';
    $strAdditionalHTML .= '<p>' . Loc::getMessage('DK_HELPER_NEW_VERSION') . '"<span style="color: green;">';
    $strAdditionalHTML .= $strVersion . '</span>"</p>';
    $strAdditionalHTML .= '<input type="submit" name="dk_update" value="' . Loc::getMessage('DK_HELPER_UPDATE_BUTTON') . '"> <br><br>';
    $strAdditionalHTML .= '</div>';
}

if (!empty($request->get('dk_update'))) {

    $isResult = \DK\Helper\Main\System::update();

    if ($isResult === false) {
        $strAdditionalHTML .= '<div>';
        $strAdditionalHTML .= '<p style="color: red;">' . Loc::getMessage('DK_HELPER_INSTALL_ERROR') . '</p>';
        $strAdditionalHTML .= '</div>';
    } else {
        LocalRedirect($_SERVER['HTTP_REFERER']);
    }
}

$obOptions = new \CModuleOptions($strModuleID, $arTabs, $arGroups, $arOptions, $isShowRightsTab, $strAdditionalHTML);
$obOptions->ShowHTML();
