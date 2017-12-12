<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 15.12.17
 * Time: 10:33
 */

IncludeModuleLangFile(__FILE__);

global $APPLICATION;
$MODULE_ID = "logger_iblock";

if ($APPLICATION->GetGroupRight("form") > "D") {
    // сформируем верхний пункт меню
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "sort" => 999,
        "url" => $MODULE_ID . "_list.php?lang=" . LANGUAGE_ID,
        "text" => GetMessage('HISTORY_OF_CHANGE'),
        "title" => GetMessage('HISTORY_OF_CHANGE'),
        "icon" => "form_menu_icon",
        "page_icon" => "form_page_icon",
        "items_id" => $MODULE_ID . "hoc",
        "items" => array(),
    );


    return $aMenu;
}

return false;
?>