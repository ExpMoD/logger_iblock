<?

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class logger_iblock extends CModule
{
    var $MODULE_ID = "logger.iblock";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;

    var $PARTNER_NAME = "Kudakov Andrey";

    // пути
    var $PATH;
    var $PATH_INSTALL;
    var $PATH_ADMIN;

    function logger_iblock()
    {
        $arModuleVersion = array();

        $this->PATH = $_SERVER['DOCUMENT_ROOT'] . getLocalPath("modules/$this->MODULE_ID");

        $this->PATH = dirname(dirname(__FILE__));
        $this->PATH_INSTALL = $this->PATH . "/install";
        $this->PATH_ADMIN = $this->PATH . "/admin";
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
            "OnBeforeIBlockElementUpdate",
            $this->MODULE_ID,
            "EditEvents",
            "onIBlockBeforeEditElement"
        );
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockElementUpdate",
            $this->MODULE_ID,
            "EditEvents",
            "onIBlockAfterEditElement"
        );
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockElementSetPropertyValuesEx",
            $this->MODULE_ID,
            "EditEvents",
            "OnIBlockAfterElementSetPropertyValuesEx"
        );


        /*** Измениние разделов ***/
        RegisterModuleDependences(
            "iblock",
            "OnBeforeIBlockSectionUpdate",
            $this->MODULE_ID,
            "EditEvents",
            "onIBlockBeforeEditSection"
        );
        RegisterModuleDependences(
            "iblock",
            "OnAfterIBlockSectionUpdate",
            $this->MODULE_ID,
            "EditEvents",
            "onIBlockAfterEditSection"
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

    function InstallFiles()
    {
        if (is_dir($this->PATH_ADMIN)) {
            AddMessage2Log("OKEY");
            if ($dir = opendir($this->PATH_ADMIN)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || $item == 'menu.php')
                        continue;
                    file_put_contents($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item,
                        '<' . '? require("' . $this->PATH_ADMIN . '/' . $item . '");?' . '>');
                }
                closedir($dir);
            }
        }
    }

    function UnInstallFiles()
    {
        if (is_dir($this->PATH_ADMIN)) {
            if ($dir = opendir($this->PATH_ADMIN)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.')
                        continue;
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item);
                }
                closedir($dir);
            }
        }
    }

    function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->InstallFiles();
        $this->RegisterEvents();
        RegisterModule($this->MODULE_ID);
        return true;
    }

    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->UnInstallFiles();
        $this->UnRegisterEvents();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }
}