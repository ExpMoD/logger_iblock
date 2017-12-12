<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 13.12.17
 * Time: 9:29
 */

/***
 * Настройки модуля
 * Обращения к параметрам происходит через Options::getOptionStr(#id#) где '#id#' название настройки
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

use logger_iblock\Options;

class EditEvents
{
    function onIBlockBeforeEditElement(&$arFields)
    {
        $iBlockIncluded = CModule::IncludeModule('iblock');

        if (Options::getOptionStr("ENABLED") == 'Y' && $iBlockIncluded) {
            $arElement = CIBlockElement::GetList(
                array(),
                array('IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID'])
                )->GetNextElement();
            $oldElement = array();
            $oldElement['FIELDS'] = $arElement->GetFields();
            $oldElement['PROPERTIES'] = $arElement->GetProperties();

            $_SESSION[Options::ses_var] = $oldElement;
        }
    }

    function onIBlockAfterEditElement(&$arFields)
    {
        $iBlockIncluded = CModule::IncludeModule('iblock');

        if (Options::getOptionStr("ENABLED") == 'Y' && $iBlockIncluded) {
            $oldElement = $_SESSION[Options::ses_var];
            unset($_SESSION[Options::ses_var]);

            if (! is_array($oldElement['FIELDS']) && ! is_array($oldElement['PROPERTIES'])) return;

            $arElement = CIBlockElement::GetList(
                array(),
                array('IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID'])
            )->GetNextElement();
            $newElement = array();
            $newElement['FIELDS'] = $arElement->GetFields();
            $newElement['PROPERTIES'] = $arElement->GetProperties();


            $IBLOCK_ID = $newElement['FIELDS']['IBLOCK_ID'];

            $ibActive = Options::getOptionStr(Options::ib . ".ACTIVE." . $IBLOCK_ID);
            $ibElFields = Options::getOptionStr(Options::ib . ".ELEMENTFIELDS." . $IBLOCK_ID);
            $ibElProps = Options::getOptionStr(Options::ib . ".ELEMENTPROPS." . $IBLOCK_ID);

            if ($ibActive == 'Y') {
                if (strlen($ibElFields))  $ibElFields  = explode(';;;', $ibElFields);
                if (strlen($ibElProps))   $ibElProps   = explode(';;;', $ibElProps);

                $changedTemp = array();


                if (! in_array('nothing', $ibElFields)) {
                    foreach ($oldElement['FIELDS'] as $key => $oldValue) {
                        if (substr($key, 0, 1) == '~') continue;

                        $newValue = $newElement['FIELDS'][$key];

                        if (is_array($newValue))
                            $newValue = implode('; ', $newValue);
                        if (is_array($oldValue))
                            $oldValue = implode('; ', $oldValue);
                        if ($oldValue != $newValue) {
                            if (in_array($key, $ibElFields) || ! $ibElFields)
                                $changedTemp['FIELDS'][$key] = $oldValue . " > " . $newValue;
                        }

                    }
                }

                if (! in_array('nothing', $ibElProps)) {
                    foreach ($oldElement['PROPERTIES'] as $key => $oldValue) {
                        if (substr($key, 0, 1) == '~') continue;

                        $newValue = $newElement['PROPERTIES'][$key]['VALUE'];
                        $oldValue = $oldValue['VALUE'];

                        if (is_array($newValue))
                            $newValue = implode('; ', $newValue);
                        if (is_array($oldValue))
                            $oldValue = implode('; ', $oldValue);

                        if ($oldValue != $newValue) {
                            if (in_array($key, $ibElProps) || ! $ibElProps)
                                $changedTemp['PROPERTIES'][$key] = $oldValue . " > " . $newValue;
                        }
                    }
                }

                $changed = array();

                foreach ($changedTemp['FIELDS'] as $key => $value)
                    $changed[] = $key . ": " . $value;

                foreach ($changedTemp['PROPERTIES'] as $key => $value)
                    $changed[] = $key . ": " . $value;

                if (!! count($changed)) {
                    \logger_iblock\HLB::add(
                        Options::ENTITY_TYPE_ELEMENT,
                        $IBLOCK_ID,
                        Options::ACTION_TYPE_EDIT,
                        $changed
                    );
                }
            }
        }
    }


    function OnIBlockAfterElementSetPropertyValuesEx($ELEMENT_ID, $IBLOCK_ID, $PROPERTY_VALUES, $FLAGS) {

        $ibActive = Options::getOptionStr(Options::ib . ".ACTIVE." . $IBLOCK_ID);
        $ibElProps = Options::getOptionStr(Options::ib . ".ELEMENTPROPS." . $IBLOCK_ID);
        if ($ibActive == 'Y') {
            if (strlen($ibElProps)) $ibElProps   = explode(';;;', $ibElProps);

            if (!in_array('nothing', $ibElProps)) {

                $changedTemp = array();

                foreach ($PROPERTY_VALUES as $key => $newValue) {
                    if (substr($key, 0, 1) == '~') continue;

                    if (is_array($newValue))
                        $newValue = implode('; ', $newValue);

                    if (in_array($key, $ibElProps) || !$ibElProps)
                        $changedTemp[$key] = $newValue;
                }

                $changed = array();

                foreach ($changedTemp as $key => $value)
                    $changed[] = $key . ": " . $value;


                if (!! count($changed)) {
                    \logger_iblock\HLB::add(
                        Options::ENTITY_TYPE_ELEMENT,
                        $IBLOCK_ID,
                        Options::ACTION_TYPE_EDIT,
                        $changed
                    );
                }
            }
        }
    }



    function onIBlockBeforeEditSection(&$arFields)
    {
        $iBlockIncluded = CModule::IncludeModule('iblock');

        if (Options::getOptionStr("ENABLED") == 'Y' && $iBlockIncluded) {
            $arElement = CIBlockSection::GetList(
                array(),
                array('IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID']),
                false,
                array('*', 'UF_*')
            )->Fetch();
            $oldElement = array();
            $oldElement['FIELDS'] = $arElement;

            $_SESSION[Options::ses_var] = $oldElement;
        }
    }


    function onIBlockAfterEditSection(&$arFields)
    {$iBlockIncluded = CModule::IncludeModule('iblock');

        if (Options::getOptionStr("ENABLED") == 'Y' && $iBlockIncluded) {
            $oldElement = $_SESSION[Options::ses_var];
            unset($_SESSION[Options::ses_var]);

            if (! is_array($oldElement['FIELDS'])) return;

            $arElement = CIBlockSection::GetList(
                array(),
                array('IBLOCK_ID' => $arFields['IBLOCK_ID'], 'ID' => $arFields['ID']),
                false,
                array('*', 'UF_*')
            )->Fetch();
            $newElement = array();
            $newElement['FIELDS'] = $arElement;


            $IBLOCK_ID = $newElement['FIELDS']['IBLOCK_ID'];

            $ibActive = Options::getOptionStr(Options::ib . ".ACTIVE." . $IBLOCK_ID);
            $ibElFields = Options::getOptionStr(Options::ib . ".SECTIONFIELDS." . $IBLOCK_ID);
            $ibElProps = Options::getOptionStr(Options::ib . ".SECTIONPROPS." . $IBLOCK_ID);

            if ($ibActive == 'Y') {
                if (strlen($ibElFields))  $ibElFields  = explode(';;;', $ibElFields);
                if (strlen($ibElProps))   $ibElProps   = explode(';;;', $ibElProps);

                $changedTemp = array();


                $isNothingFields = in_array('nothing', $ibElFields);
                $isNothingProps = in_array('nothing', $ibElProps);

                if (! in_array('nothing', $ibElFields)) {
                    foreach ($oldElement['FIELDS'] as $key => $oldValue) {
                        if (substr($key, 0, 1) == '~') continue;

                        $newValue = $newElement['FIELDS'][$key];

                        if (is_array($newValue))
                            $newValue = implode('; ', $newValue);
                        if (is_array($oldValue))
                            $oldValue = implode('; ', $oldValue);

                        if ($oldValue != $newValue) {
                            if (substr($key, 0, 3) != 'UF_') {
                                if ((in_array($key, $ibElFields) || ! $ibElFields) && ! $isNothingFields)
                                    $changedTemp['FIELDS'][$key] = $oldValue . " > " . $newValue;
                            } else {
                                if ((in_array($key, $ibElProps) || ! $ibElProps) && ! $isNothingProps)
                                    $changedTemp['PROPERTIES'][$key] = $oldValue . " > " . $newValue;
                            }
                        }
                    }
                }

                $changed = array();

                foreach ($changedTemp['FIELDS'] as $key => $value)
                    $changed[] = $key . ": " . $value;

                foreach ($changedTemp['PROPERTIES'] as $key => $value)
                    $changed[] = $key . ": " . $value;

                if (!! count($changed)) {
                    \logger_iblock\HLB::add(
                        Options::ENTITY_TYPE_SECTION,
                        $IBLOCK_ID,
                        Options::ACTION_TYPE_EDIT,
                        $changed
                    );
                }
            }
        }
    }
}