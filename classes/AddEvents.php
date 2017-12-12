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

class AddEvents
{
    function onIBlockAddElement(&$arFields)
    {
        if (Options::getOptionStr("ENABLED") == 'Y') {
            $ibActive = Options::getOptionStr(Options::ib . ".ACTIVE." . $arFields['IBLOCK_ID']);
            if ($ibActive == 'Y') {
                \logger_iblock\HLB::add(
                    Options::ENTITY_TYPE_ELEMENT,
                    $arFields['ID'],
                    Options::ACTION_TYPE_ADD,
                    array()
                );
            }
        }
    }

    function onIBlockAddSection(&$arFields)
    {
        if (Options::getOptionStr("ENABLED") == 'Y') {
            $ibActive = Options::getOptionStr(Options::ib . ".ACTIVE." . $arFields['IBLOCK_ID']);
            if ($ibActive == 'Y') {
                \logger_iblock\HLB::add(
                    Options::ENTITY_TYPE_SECTION,
                    $arFields['ID'],
                    Options::ACTION_TYPE_ADD,
                    array()
                );
            }
        }
    }
}