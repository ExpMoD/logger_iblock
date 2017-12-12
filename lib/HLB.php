<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 14.12.17
 * Time: 9:45
 */

namespace logger_iblock;

use Bitrix\Main\Type\DateTime;
use logger_iblock\Options;

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
\Bitrix\Main\Loader::includeModule('highloadblock');

class HLB
{
    public static function createHLB()
    {
        $HLB_ID = Options::getOptionInt(Options::HLB_ID_OPTION);
        if (! $HLB_ID) {
            $highloadBlockData = array ( 'NAME' => 'LoggerIBlock', 'TABLE_NAME' => 'logger_iblock' );
            $result = HLBT::add($highloadBlockData);
            $HLB_ID = $result->getId();

            if(! $HLB_ID){
                return false;
            } else {
                Options::setOptionInt(Options::HLB_ID_OPTION, $HLB_ID);

                foreach (Options::UF_NAMES as $UF) {
                    self::createUF(
                        $UF['NAME'],
                        $UF['TYPE'],
                        $UF['RULABEL'],
                        $UF['MULTIPLE'],
                        $UF['SORT'],
                        $UF['MANDATORY'],
                        $UF['SETTINGS']
                    );
                }

                return $HLB_ID;
            }
        } else {
            return $HLB_ID;
        }
    }

    public static function createUF($name,
                                    $type,
                                    $ruLabel,
                                    $multiple = false,
                                    $sort = 500,
                                    $mandatory = false,
                                    $settings = false)
    {
        if ($HLB_ID = Options::getOptionInt(Options::HLB_ID_OPTION)) {
            $userTypeEntity = new \CUserTypeEntity();

            $UFname = "UF_$name";

            $userTypeData = array(
                'ENTITY_ID' => 'HLBLOCK_' . $HLB_ID,
                'FIELD_NAME' => $UFname,
                'USER_TYPE_ID' => $type,
                'XML_ID' => 'XML_ID_' . $name,
                'SORT' => $sort,
                'MULTIPLE' => ($multiple) ? "Y" : "N",
                'MANDATORY' => ($mandatory) ? "Y" : "N",
                'SHOW_FILTER' => 'Y',
                'SHOW_IN_LIST' => '',
                'EDIT_IN_LIST' => '',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' => $settings,
                'EDIT_FORM_LABEL' => array(
                    'ru' => $ruLabel,
                ),
                'LIST_COLUMN_LABEL' => array(
                    'ru' => $ruLabel,
                ),
                'LIST_FILTER_LABEL' => array(
                    'ru' => $ruLabel,
                ),
                'ERROR_MESSAGE' => array(
                    'ru' => 'Ошибка при заполнении пользовательского свойства <Названия свойства>',
                    'en' => 'An error in completing the user field <Property name>',
                ),
                'HELP_MESSAGE' => array(
                    'ru' => '',
                    'en' => '',
                ),
            );

            $userTypeId = $userTypeEntity->Add($userTypeData);
        }
    }

    public static function getEntityDataClass($HlBlockId)
    {
        if (empty($HlBlockId) || $HlBlockId < 1) {
            return false;
        }
        $hlblock = HLBT::getById($HlBlockId)->fetch();
        $entity = HLBT::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }

    public static function add($entityType, $entityId, $actionType, $data)
    {
        if ($HlBlockId = self::createHLB()) {
            global $USER;

            $dataClass = self::getEntityDataClass($HlBlockId);

            $blockData = array();

            foreach (Options::UF_NAMES as $UF) {
                $key = "UF_" . $UF['NAME'];
                switch ($UF['LOCAL_ID']) {
                    case 1:
                        $blockData[$key] = $entityType;
                        break;
                    case 2:
                        $blockData[$key] = $entityId;
                        break;
                    case 3:
                        $blockData[$key] = $actionType;
                        break;
                    case 4:
                        $dataTime = new DateTime();
                        $blockData[$key] = $dataTime->toString();
                        break;
                    case 5:
                        $blockData[$key] = $USER->GetID() . " (" . $USER->GetLogin() . ") " . $USER->GetFullName();
                        break;
                    case 6:
                        $blockData[$key] = $data;
                        break;
                }
            }

            $dataClass::add($blockData);
        } else {
            return false;
        }
    }
}