<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 15.12.17
 * Time: 10:41
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


// Настройки
$ROWS_PER_PAGE = 20;





// ID таблицы
$hlblock_id = Options::getOptionInt(Options::HLB_ID_OPTION);


$hlblock = HLBT::getById($hlblock_id)->fetch();


$entity = HLBT::compileEntity($hlblock);


// Информация по пользовательским полям
$fields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields('HLBLOCK_' . $hlblock['ID'], 0, LANGUAGE_ID);

// Сортировка
$sort_id = 'ID';
$sort_type = 'DESC';
if (!empty($_GET['sort_id']) && (isset($fields[$_GET['sort_id']]))) {
    $sort_id = $_GET['sort_id'];
}
if (!empty($_GET['sort_type']) && in_array($_GET['sort_type'], array('ASC', 'DESC'), true)) {
    $sort_type = $_GET['sort_type'];
}


// Пагинация
if (isset($ROWS_PER_PAGE) && $ROWS_PER_PAGE > 0) {
    $pagenId = isset($PAGEN_ID) && trim($PAGEN_ID) != '' ? trim($PAGEN_ID) : 'page';
    $perPage = intval($ROWS_PER_PAGE);
    $nav = new \Bitrix\Main\UI\AdminPageNavigation($pagenId);
    $nav->allowAllRecords(true)
        ->setPageSize($perPage)
        ->initFromUri();
} else {
    $arParams['ROWS_PER_PAGE'] = 0;
}





// Начало запросов
$mainQuery = new Entity\Query($entity);
$mainQuery->setSelect(array('*'));
$mainQuery->setOrder(array($sort_id => $sort_type));


// Фильтр
$filter = array();
if ($UF_ET = $_GET['FIND_ENTITY_TYPE']) {
    $filter['UF_' . Options::getUFNamesByLocalId(1)['NAME']] = $UF_ET;
}

if ($UF_EID = $_GET['FIND_ENTITY_ID']) {
    $filter['UF_' . Options::getUFNamesByLocalId(2)['NAME']] = $UF_EID;
}

if ($UF_AT = $_GET['FIND_ACTION_TYPE']) {
    $filter['UF_' . Options::getUFNamesByLocalId(3)['NAME']] = $UF_AT;
}

if ($_GET['FIND_DOC_FROM'] || $_GET['FIND_DOC_TO']) {
    $format = 'd.m.Y H:i:s';

    $dateFromValid = Options::isValidDateTimeString($_GET['FIND_DOC_FROM']);
    $dateToValid = Options::isValidDateTimeString($_GET['FIND_DOC_TO']);

    if ($dateFromValid) {
        $dateFrom = new DateTime($_GET['FIND_DOC_FROM']);
    } else {
        $_GET['FIND_DOC_FROM'] = "";
    }

    if ($dateToValid) {
        $dateTo = new DateTime($_GET['FIND_DOC_TO']);
    } else {
        $_GET['FIND_DOC_TO'] = "";
    }


    if ($dateFromValid && $dateToValid) {
        $filter[] = array(
            'LOGIC' => 'AND',
            '>=UF_DATE_OF_CHANGE' => $dateFrom->format($format),
            '<=UF_DATE_OF_CHANGE' => $dateTo->format($format),
        );
    } else if ($dateFromValid && ! $dateToValid) {
        $filter['>=UF_DATE_OF_CHANGE'] = $dateFrom->format($format);
    } else if (! $dateFromValid && $dateToValid) {
        $filter['<=UF_DATE_OF_CHANGE'] = $dateTo->format($format);
    }
}

if ($UF_AT = $_GET['FIND_EDITING_USER']) {
    $filter['UF_' . Options::getUFNamesByLocalId(5)['NAME']] = $UF_AT;
}

if (count($filter)) {
    $mainQuery->setFilter($filter);
    $filterIsActive = true;
}





// Пагинация
if ($perPage > 0) {
    $mainQueryCnt = $mainQuery;
    $result = $mainQueryCnt->exec();
    $result = new CDBResult($result);
    $nav->setRecordCount($result->selectedRowsCount());
    unset($mainQueryCnt, $result);

    $mainQuery->setLimit($nav->getLimit());
    $mainQuery->setOffset($nav->getOffset());
}


$result = $mainQuery->exec();
$result = new CDBResult($result);







require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
/*** ФИЛЬТР ***/
?>
<script>
function onSubmitMethod(element) {
    var form = document.forms[element.name];
    var elements = form.elements;
    for (var i = 0; i < form.elements.length; i++) {
        if (! form.elements[i].value) {
            form.elements[i].disabled = true;
        }
    }
}
</script>
<form method="GET" name="find_form" id="FIND_FORM" action="<?=$APPLICATION->GetCurPage()?>" onsubmit="onSubmitMethod(this);">
    <div class="adm-filter-wrap" style="display: block;">
        <table class="adm-filter-main-table">
            <tbody>
            <tr>
                <td class="adm-filter-main-table-cell">
                    <div class="adm-filter-content">
                        <div class="adm-filter-content-table-wrap">
                            <table cellspacing="0" class="adm-filter-content-table" style="display: table;">
                                <tbody>
                                <tr style="display: table-row;">
                                    <td class="adm-filter-item-left">Тип сущности:</td>
                                    <td class="adm-filter-item-center">
                                        <div class="adm-filter-alignment">
                                            <div class="adm-filter-box-sizing">
                                                <span class="adm-select-wrap">
                                                    <select name="FIND_ENTITY_TYPE" class="adm-select">
                                                        <option value="">(любой)</option>
                                                        <option value="ELEMENT" <?=($_GET['FIND_ENTITY_TYPE'] == 'ELEMENT') ? "selected" : ""?>><?=GetMessage('FIELD_ET_ELEMENT')?></option>
                                                        <option value="SECTION" <?=($_GET['FIND_ENTITY_TYPE'] == 'SECTION') ? "selected" : ""?>><?=GetMessage('FIELD_ET_SECTION')?></option>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>


                                <tr style="display: table-row;">
                                    <td class="adm-filter-item-left">ID (начальный и конечный):</td>
                                    <td nowrap="" class="adm-filter-item-center">
                                        <div class="adm-filter-alignment">
                                            <div class="adm-filter-box-sizing">
                                                <div class="adm-input-wrap">
                                                    <input type="text" name="FIND_ENTITY_ID" size="10" value="" class="adm-input">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>


                                <tr style="display: table-row;">
                                    <td class="adm-filter-item-left">Тип действия:</td>
                                    <td class="adm-filter-item-center">
                                        <div class="adm-filter-alignment">
                                            <div class="adm-filter-box-sizing">
                                                <span class="adm-select-wrap">
                                                    <select name="FIND_ACTION_TYPE" class="adm-select">
                                                        <option value="">(любой)</option>
                                                        <option value="EDIT" <?=($_GET['FIND_ACTION_TYPE'] == 'EDIT') ? "selected" : ""?>><?=GetMessage('FIELD_AT_EDIT')?></option>
                                                        <option value="ADD" <?=($_GET['FIND_ACTION_TYPE'] == 'ADD') ? "selected" : ""?>><?=GetMessage('FIELD_AT_ADD')?></option>
                                                        <option value="DELETE" <?=($_GET['FIND_ACTION_TYPE'] == 'DELETE') ? "selected" : ""?>><?=GetMessage('FIELD_AT_DELETE')?></option>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>


                                <tr style="display: table-row;">
                                    <td class="adm-filter-item-left">Дата изменения:</td>
                                    <td class="adm-filter-item-center">
                                        <div class="adm-calendar-block adm-filter-alignment">
                                            <div class="adm-filter-box-sizing">
                                                <div class="adm-input-wrap adm-calendar-inp adm-calendar-first" style="display: inline-block;">
                                                    <input type="text"
                                                           class="adm-input adm-calendar-from"
                                                           name="FIND_DOC_FROM"
                                                           size="15"
                                                           value="<?=$_GET['FIND_DOC_FROM']?>">
                                                    <span class="adm-calendar-icon"
                                                          title="Нажмите для выбора даты"
                                                          onclick="BX.calendar({node:this, field:'FIND_DOC_FROM', form: '', bTime: true, bHideTime: false});">
                                                    </span>
                                                </div>
                                                <span class="adm-calendar-separate" style="display: inline-block"></span>
                                                <div class="adm-input-wrap adm-calendar-second" style="display: inline-block;">
                                                    <input type="text"
                                                           class="adm-input adm-calendar-to"
                                                           name="FIND_DOC_TO"
                                                           size="15"
                                                           value="<?=$_GET['FIND_DOC_TO']?>">
                                                    <span class="adm-calendar-icon"
                                                          title="Нажмите для выбора даты"
                                                          onclick="BX.calendar({node:this, field:'FIND_DOC_TO', form: '', bTime: true, bHideTime: false});">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>


                                <tr style="display: table-row;">
                                    <td class="adm-filter-item-left">ID пользователя:</td>
                                    <td nowrap="" class="adm-filter-item-center">
                                        <div class="adm-filter-alignment">
                                            <div class="adm-filter-box-sizing">
                                                <div class="adm-input-wrap">
                                                    <input type="text"
                                                           name="FIND_EDITING_USER"
                                                           size="10"
                                                           class="adm-input"
                                                           value="<?=$_GET['FIND_EDITING_USER']?>">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="adm-filter-bottom-separate"
                             style="display: block;"></div>

                        <div class="adm-filter-bottom">
                            <input type="submit" class="adm-btn"
                                   name="set_filter" title="Найти"
                                   value="Найти">
                            <input type="reset" class="adm-btn"
                                   name="del_filter" title="Отменить"
                                   onclick="document.location.href='<?=$APPLICATION->GetCurPage()?>'"
                                   value="Отменить">
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</form>









<div class="adm-list-table-wrap">
    <table class="adm-list-table">
        <thead>
            <tr class="adm-list-table-header">
                <td class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">ID</div>
                </td>
                <td class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">Тип сущности</div>
                </td>
                <td class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">ID сущности</div>
                </td>
                <td class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">Тип действия</div>
                </td>
                <td class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">Дата изменения</div>
                </td>
                <td class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">Пользователь</div>
                </td>
            </tr>
        </thead>

        <tbody>
        <? while ($row = $result->fetch()): ?>
            <?
            $cUser = CUser::GetByID(intval($row['UF_EDITING_USER']))->Fetch();
            $curUser = "<a href='/bitrix/admin/user_edit.php?lang=ru&ID={$cUser['ID']}'>{$cUser['ID']} ({$cUser['LOGIN']}) {$cUser['LAST_NAME']} {$cUser['NAME']}</a>";
            ?>

            <tr class="adm-list-table-row">
                <td class="adm-list-table-cell align-center">
                    <?=$row['ID']?>
                </td>
                <td class="adm-list-table-cell">
                    <?=GetMessage('FIELD_ET_' . $row['UF_ENTITY_TYPE'])?>
                </td>
                <td class="adm-list-table-cell align-center">
                    <?=$row['UF_ENTITY_ID']?>
                </td>
                <td class="adm-list-table-cell">
                    <?=GetMessage('FIELD_AT_' . $row['UF_ACTION_TYPE'])?>
                </td>
                <td class="adm-list-table-cell align-right">
                    <?=$row['UF_DATE_OF_CHANGE']?>
                </td>
                <td class="adm-list-table-cell align-right">
                    <?=$curUser?>
                </td>
            </tr>
        <? endwhile; ?>
        </tbody>
    </table>
</div>
<?php

$APPLICATION->IncludeComponent(
    "bitrix:main.pagenavigation",
    "modern",
    array(
        "NAV_OBJECT" => $nav,
        "SEF_MODE" => "N"
    ),
    false
);
?>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
