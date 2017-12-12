<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 19.12.17
 * Time: 15:52
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule('logger_iblock');
CModule::IncludeModule('highloadblock');

use logger_iblock\Options;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Main\Entity;

// подключим языковой файл
IncludeModuleLangFile(__FILE__);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight(Options::module_id);

if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


// ID таблицы
$hlblock_id = Options::getOptionInt(Options::HLB_ID_OPTION);

$hlblock = HLBT::getById($hlblock_id)->fetch();

$entity = HLBT::compileEntity($hlblock);


// Начало запросов
$mainQuery = new Entity\Query($entity);
$mainQuery->setSelect(array('*'));

$ROW_ID = intval($_GET['id']);
if ($ROW_ID > 0) {
    $mainQuery->setFilter(array('ID' => $ROW_ID));
}


$result = $mainQuery->exec();
$result = new CDBResult($result);


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?
$element = $result->Fetch();
if (count($element)):
    ?>
    <div class="adm-detail-toolbar">
        <a href="<?= Options::module_id . "_list.php?lang=" . LANGUAGE_ID ?>" class="adm-detail-toolbar-btn"
           title="Вернуться в список" id="btn_list"><span class="adm-detail-toolbar-btn-l"></span><span
                    class="adm-detail-toolbar-btn-text">Вернуться в список</span><span
                    class="adm-detail-toolbar-btn-r"></span></a>
    </div>

    <div class="adm-detail-block">
        <div class="adm-detail-content-wrap">
            <div class="adm-detail-content">
                <div class="adm-detail-content-item-block">
                    <table class="adm-detail-content-table edit-table">
                        <tbody>
                        <tr>
                            <td width="40%" class="adm-detail-content-cell-l">ID</td>
                            <td class="adm-detail-content-cell-r">
                                <?= $element['ID'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" class="adm-detail-content-cell-l">
                                Тип сущности:
                            </td>
                            <td width="60%" class="adm-detail-content-cell-r">
                                <?= GetMessage('FIELD_ET_' . $element['UF_' . Options::getUFNamesByLocalId(1)['NAME']]) ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" class="adm-detail-content-cell-l">
                                ID сущности:
                            </td>
                            <td width="60%" class="adm-detail-content-cell-r">
                                <?= $element['UF_' . Options::getUFNamesByLocalId(2)['NAME']] ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" class="adm-detail-content-cell-l">
                                Тип действия:
                            </td>
                            <td width="60%" class="adm-detail-content-cell-r">
                                <?= GetMessage('FIELD_AT_' . $element['UF_' . Options::getUFNamesByLocalId(3)['NAME']]) ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" class="adm-detail-content-cell-l">
                                Дата изменения:
                            </td>
                            <td width="60%" class="adm-detail-content-cell-r">
                                <?= $element['UF_' . Options::getUFNamesByLocalId(4)['NAME']] ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" class="adm-detail-content-cell-l">
                                Пользователь, внесший изменение:
                            </td>
                            <td width="60%" class="adm-detail-content-cell-r">
                                <?= $element['UF_' . Options::getUFNamesByLocalId(5)['NAME']] ?>
                            </td>
                        </tr>
                        <?
                        $dataChanged = $element['UF_' . Options::getUFNamesByLocalId(6)['NAME']];

                        if (count($dataChanged) > 0):
                            ?>
                            <tr class="heading">
                                <td colspan="2">Изменения:</td>
                            </tr>
                            <? foreach ($dataChanged as $item): ?>
                            <?
                            $firstArray = explode(': ', $item);
                            ?>
                            <tr>
                                <td class="adm-detail-valign-top adm-detail-content-cell-l">
                                    <?= trim($firstArray[0]) ?>:
                                </td>
                                <td class="adm-detail-content-cell-r">
                                    <?= trim($firstArray[1]) ?>
                                </td>
                            </tr>
                        <? endforeach; ?>
                        <? endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="height: 14px;"></div>
        </div>
    </div>
<? endif; ?>


<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>













