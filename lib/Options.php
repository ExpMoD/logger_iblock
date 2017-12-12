<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 14.12.17
 * Time: 10:39
 */

namespace logger_iblock;


class Options
{
    const ib = "iblock";
    const module_id = "logger.iblock";
    const ses_var = "IBLOCK_LOGGER_ELEMENT";

    const ACTION_TYPE_EDIT = "EDIT";
    const ACTION_TYPE_ADD = "ADD";
    const ACTION_TYPE_DELETE = "DELETE";

    const ENTITY_TYPE_ELEMENT = "ELEMENT";
    const ENTITY_TYPE_SECTION = "SECTION";

    const HLB_ID_OPTION = "HLB.ID";
    const UF_NAMES = array(
        array(
            'LOCAL_ID' => '1',
            'NAME' => "ENTITY_TYPE",
            'TYPE' => "string",
            'RULABEL' => "Тип сущности",
            'MULTIPLE' => false,
            'SORT' => '500'
        ),
        array(
            'LOCAL_ID' => '2',
            'NAME' => "ENTITY_ID",
            'TYPE' => "integer",
            'RULABEL' => "ID сущности",
            'MULTIPLE' => false,
            'SORT' => '501'
        ),
        array(
            'LOCAL_ID' => '3',
            'NAME' => "ACTION_TYPE",
            'TYPE' => "string",
            'RULABEL' => "Тип действия",
            'MULTIPLE' => false,
            'SORT' => '502'
        ),
        array(
            'LOCAL_ID' => '4',
            'NAME' => "DATE_OF_CHANGE",
            'TYPE' => "datetime",
            'RULABEL' => "Дата изменения",
            'MULTIPLE' => false,
            'SORT' => '503',
            'SETTINGS' => array(
                'DEFAULT_VALUE' => ['TYPE' => 'NOW']
            )
        ),
        array(
            'LOCAL_ID' => '5',
            'NAME' => "EDITING_USER",
            'TYPE' => "string",
            'RULABEL' => "Пользователь, внесший изменение",
            'MULTIPLE' => false,
            'SORT' => '504'
        ),
        array(
            'LOCAL_ID' => '6',
            'NAME' => "DATA",
            'TYPE' => "string",
            'RULABEL' => "Изменения",
            'MULTIPLE' => true,
            'SORT' => '505',
            'SETTINGS' => array(
                'SIZE' => '50'
            )
        ),
    );

    public static function getOptionInt($name)
    {
        return \COption::GetOptionInt(self::module_id, $name);
    }

    public static function getOptionStr($name)
    {
        return \COption::GetOptionString(self::module_id, $name);
    }

    public static function setOptionInt($name, $value)
    {
        return \COption::SetOptionString(self::module_id, $name, $value);
    }

    public static function setOptionStr($name, $value)
    {
        return \COption::SetOptionString(self::module_id, $name, $value);
    }



    public static function getUFNamesByLocalId($ID)
    {
        foreach (self::UF_NAMES as $UF) {
            if ($UF['LOCAL_ID'] == $ID) {
                return $UF;
            }
        }

        return false;
    }


    public static function isValidDateTimeString($str_dt, $str_dateformat = 'd.m.Y H:i:s') {
        $date = \DateTime::createFromFormat($str_dateformat, $str_dt);
        return $date && \DateTime::getLastErrors()["warning_count"] == 0 && \DateTime::getLastErrors()["error_count"] == 0;
    }
}