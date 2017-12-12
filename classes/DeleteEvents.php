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

class DeleteEvents
{
    const ib = "iblock";
    const module_id = "logger_iblock";

    function onIBlockDeleteElement($ID)
    {

    }

    function onIBlockDeleteSection($ID)
    {

    }
}