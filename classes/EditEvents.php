<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 13.12.17
 * Time: 9:29
 */

/***
 * Настройки модуля
 * Обращения к параметрам происходит через COption::GetOptionString(#id#) где '#id#' название настройки
 * -- Включено ли логирование: main.ENABLED
 *
 * Настройки информационных блоков
 * Идентификаторы строются по шаблону где '#IBlockId#' id инфоблока
 * -- Логировать ли инфоблок: iblock.ACTIVE.#IBlockId#
 * -- Логируемые поля элементов ИБ: iblock.ELEMENTFIELDS.#IBlockId#
 * -- Логируемые свойства элементов ИБ: iblock.ELEMENTPROPS.#IBlockId#
 * -- Логируемые поля разделов ИБ: iblock.SECTIONFIELDS.#IBlockId#
 * -- Логируемые свойства разделов ИБ: iblock.SECTIONPROPS.#IBlockId#
 */

$global_value = "";



class EditEvents
{
    const ib = "iblock";
    const module_id = "logger_iblock";
    const ses_var = "IBLOCK_LOGGER_ELEMENT";

    function onIBlockBeforeEditElement(&$arFields)
    {
        $iBlockIncluded = CModule::IncludeModule('iblock');

        if (self::getOption("main.ENABLED") == 'Y' && $iBlockIncluded) {
            $arElement = CIBlockElement::GetList(
                array(),
                array('IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID'])
                )->GetNextElement();
            $oldElement = array();
            $oldElement['FIELDS'] = $arElement->GetFields();
            $oldElement['PROPERTIES'] = $arElement->GetProperties();

            $_SESSION[self::ses_var] = $oldElement;
        }
    }

    function onIBlockAfterEditElement(&$arFields)
    {
        $iBlockIncluded = CModule::IncludeModule('iblock');

        if (self::getOption("main.ENABLED") == 'Y' && $iBlockIncluded) {
            $oldElement = $_SESSION[self::ses_var];
            unset($_SESSION[self::ses_var]);

            //if (! is_array($oldElement['FIELDS']) && ! is_array($oldElement['PROPERTIES'])) return;

            $arElement = CIBlockElement::GetList(
                array(),
                array('IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID'])
            )->GetNextElement();
            $newElement = array();
            $newElement['FIELDS'] = $arElement->GetFields();
            $newElement['PROPERTIES'] = $arElement->GetProperties();


            $IBLOCK_ID = $newElement['FIELDS']['IBLOCK_ID'];

            global $APPLICATION;
            $ibActive = self::getOption(self::ib . ".ACTIVE." . $IBLOCK_ID);
            $ibElFields = self::getOption(self::ib . ".ELEMENTFIELDS." . $IBLOCK_ID);
            $ibElProps = self::getOption(self::ib . ".ELEMENTPROPS." . $IBLOCK_ID);
            $ibSecFields = self::getOption(self::ib . ".SECTIONFIELDS." . $IBLOCK_ID);
            $ibSecProps = self::getOption(self::ib . ".SECTIONPROPS." . $IBLOCK_ID);


            if ($ibActive == 'Y') {
                if (strlen($ibElFields))  $ibElFields  = explode(';;;', $ibElFields);
                if (strlen($ibElProps))   $ibElProps   = explode(';;;', $ibElProps);
                if (strlen($ibSecFields)) $ibSecFields = explode(';;;', $ibSecFields);
                if (strlen($ibSecProps))  $ibSecProps  = explode(';;;', $ibSecProps);

                $changed = array();


                if (! in_array('nothing', $ibElFields)) {
                    foreach ($oldElement['FIELDS'] as $key => $oldValue) {
                        $newValue = $newElement['FIELDS'][$key];

                        if (is_array($newValue))
                            $newValue = implode('; ', $newValue);
                        if (is_array($oldValue))
                            $oldValue = implode('; ', $oldValue);

                        if ($oldValue != $newValue && substr($key, 0, 1) != '~') {
                            if (in_array($key, $ibElFields) || ! $ibElProps)
                                $changed['FIELDS'][$key] = $oldValue . ">>>>" . $newValue;
                        }
                    }
                }

                if (! in_array('nothing', $ibElProps)) {
                    foreach ($oldElement['PROPERTIES'] as $key => $oldValue) {
                        $newValue = $newElement['PROPERTIES'][$key]['VALUE'];
                        $oldValue = $oldValue['VALUE'];

                        if (is_array($newValue))
                            $newValue = implode('; ', $newValue);
                        if (is_array($oldValue))
                            $oldValue = implode('; ', $oldValue);

                        if ($oldValue != $newValue && substr($key, 0, 1) != '~') {
                            if (in_array($key, $ibElProps) || ! $ibElProps)
                                $changed['PROPERTIES'][$key] = $oldValue . ">>>>" . $newValue;
                        }
                    }
                }

                AddMessage2Log(mydump($changed));
            }
        }
    }




    function onIBlockBeforeEditSection(&$arFields)
    {

    }


    function onIBlockAfterEditSection(&$arFields)
    {

    }



    function getOption($name)
    {
        return COption::GetOptionString(self::module_id, $name);
    }
}



/*

            $newProps = $newElement['PROPERTY_VALUES'];
            unset($newElement['PROPERTY_VALUES']);
            $newFields = $newElement;

            $oldProps = $oldElement['PROPERTY_VALUES'];
            unset($oldElement['PROPERTY_VALUES']);
            $oldFields = $oldElement;

            $iBlockIncluded = CModule::IncludeModule('iblock');

            if ($iBlockIncluded) {

            }*/



//$sda = CIBlockElement::GetProperty($newFields['IBLOCK_ID'] , $newFields['ID'], array(), array());

//AddMessage2Log(mydump($newProps));
//AddMessage2Log(mydump($oldFields));
/*
global $APPLICATION;
$ibActive = self::getOption(self::ib . ".ACTIVE." . $newFields['IBLOCK_ID']);
$ibElFields = self::getOption(self::ib . ".ELEMENTFIELDS." . $newFields['IBLOCK_ID']);
$ibElProps = self::getOption(self::ib . ".ELEMENTPROPS." . $newFields['IBLOCK_ID']);
$ibSecFields = self::getOption(self::ib . ".SECTIONFIELDS." . $newFields['IBLOCK_ID']);
$ibSecProps = self::getOption(self::ib . ".SECTIONPROPS." . $newFields['IBLOCK_ID']);
*/


/*if ($ibActive == 'Y' && CModule::IncludeModule('iblock')) {
    if (strlen($ibElFields))  $ibElFields  = explode(';;;', $ibElFields);
    if (strlen($ibElProps))   $ibElProps   = explode(';;;', $ibElProps);
    if (strlen($ibSecFields)) $ibSecFields = explode(';;;', $ibSecFields);
    if (strlen($ibSecProps))  $ibSecProps  = explode(';;;', $ibSecProps);

    $changed = array();

    $arOldElement = CIBlockElement::GetList(
        array(),
        array('IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID'])
    )->GetNextElement();
    $arOldFields = $arOldElement->GetFields();
    $arOldProps = $arOldElement->GetProperties();

    $arElFields = CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "LIST_SETTINGS")['VALUES'];
    foreach ($arOldFields as $key => $oldValue) {
        $newValue = $arFields[$key];
        if (in_array($key, array_keys($arElFields)) && $newValue) {
            if ($oldValue != $newValue) {
                if ($key == 'PREVIEW_PICTURE' || $key == 'DETAIL_PICTURE') {
                    $changed[$key] = $oldValue . " >> " . $newValue['old_file'];
                } else {
                    $changed[$key] = $oldValue . " >> " . $newValue;
                }
            }
        }
    }

    $APPLICATION->ThrowException(mydump($arFields));
    return false;
}*/