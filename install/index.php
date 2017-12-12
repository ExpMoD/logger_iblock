<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class logger_iblock extends CModule
{
    var $MODULE_ID = "logger_iblock";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    // пути
    var $PATH;
    var $PATH_INSTALL;

    function logger_iblock()
    {
        $arModuleVersion = array();

        $this->PATH = $_SERVER['DOCUMENT_ROOT'] . "/$this->MODULE_MODE_EXEC/modules/$this->MODULE_ID";
        $this->PATH_INSTALL = "$this->PATH/install";
        include($this->PATH_INSTALL . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = GetMessage('MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('MODULE_DESCRIPTION');
    }

    function RegisterEvents()
    {
        /*** Добавление элементов ***/
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockElementAdd",
            $this->MODULE_ID,
            "AddEvents",
            "onIBlockAddElement"
        );


        /*** Добавление разделов ***/
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockSectionAdd",
            $this->MODULE_ID,
            "AddEvents",
            "onIBlockAddSection"
        );


        /*** Измениние элементов ***/
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockElementUpdate",
            $this->MODULE_ID,
            "EditEvents",
            "onIBlockEditElement"
        );


        /*** Измениние разделов ***/
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockSectionUpdate",
            $this->MODULE_ID,
            "EditEvents",
            "onIBlockEditSection"
        );


        /*** Удаление элементов ***/
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockElementDelete",
            $this->MODULE_ID,
            "DeleteEvents",
            "onIBlockDeleteElement"
        );


        /*** Удаление разделов ***/
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockSectionDelete",
            $this->MODULE_ID,
            "DeleteEvents",
            "onIBlockDeleteSection"
        );
    }

    function UnRegisterEvents()
    {
        /*** Добавление элементов ***/
        UnRegisterModuleDependences(
            "iblock",
            "OnAfterIBlockElementAdd",
            $this->MODULE_ID,
            "AddEvents",
            "onIBlockAddElement"
        );


        /*** Добавление разделов ***/
        UnRegisterModuleDependences(
            "iblock",
            "OnAfterIBlockSectionAdd",
            $this->MODULE_ID,
            "AddEvents",
            "onIBlockAddSection"
        );


        /*** Измениние элементов ***/
        UnRegisterModuleDependences(
            "iblock",
            "OnAfterIBlockElementUpdate",
            $this->MODULE_ID,
            "EditEvents",
            "onIBlockEditElement"
        );


        /*** Измениние разделов ***/
        UnRegisterModuleDependences(
            "iblock",
            "OnAfterIBlockSectionUpdate",
            $this->MODULE_ID,
            "EditEvents",
            "onIBlockEditSection"
        );


        /*** Удаление элементов ***/
        UnRegisterModuleDependences(
            "iblock",
            "OnAfterIBlockElementDelete",
            $this->MODULE_ID,
            "DeleteEvents",
            "onIBlockDeleteElement"
        );


        /*** Удаление разделов ***/
        UnRegisterModuleDependences(
            "iblock",
            "OnAfterIBlockSectionDelete",
            $this->MODULE_ID,
            "DeleteEvents",
            "onIBlockDeleteSection"
        );
    }


    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->RegisterEvents();
        RegisterModule($this->MODULE_ID);
        return true;
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnRegisterEvents();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }
}