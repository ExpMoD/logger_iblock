<h1>Настройка модуля</h1>

<?
$module_id = "logger_iblock";
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);
$RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($RIGHT >= "R"):
    $iBlockIncluded = false;
    if (CModule::IncludeModule('iblock'))
        $iBlockIncluded = true;

    $arTabs = array(
        array(
            "DIV" => "main",
            "TAB" => GetMessage("MAIN_TAB_SET"),
            "ICON" => "perfmon_settings",
            "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")
        ),
        array(
            "DIV" => "rights",
            "TAB" => GetMessage("MAIN_TAB_RIGHTS"),
            "ICON" => "perfmon_settings",
            "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
        ),
    );

    if ($iBlockIncluded) {
        $iBlocksTab = array(
            array(
                "DIV" => "iblocks",
                "TAB" => GetMessage("LIB_TAB_IBLOCKS"),
                "ICON" => "perfmon_settings",
                "TITLE" => GetMessage("LIB_TAB_TITLE_IBLOCKS")
            )
        );
        array_splice($arTabs, 1, 0, $iBlocksTab);
    }

    $arMainOptions = array(
        GetMessage("MAIN_OPTIONS"),
        array(
            "ID" => "ENABLED",
            "NAME" => GetMessage("LIB_OPTIONS_ENABLED"),
            "TYPE" => "checkbox",
            "SIZE" => false,
            "DEFAULT" => "Y"),
    );

    $arIBlockOptions = array();

    if ($iBlockIncluded) {
        $arIBlocks = CIBlock::GetList();

        while ($iblock = $arIBlocks->GetNext()) {
            $arIBlockOptions[] = $iblock['NAME'] /*. " ({$iblock['CODE']})"*/;

            $opActive = array();
            $opActive['ID'] = "iblock.ACTIVE." . $iblock['ID'];
            $opActive['NAME'] = GetMessage('LIB_OPTIONS_ENABLED');
            $opActive['TYPE'] = "checkbox";
            $opActive['DEFAULT'] = false;
            $arIBlockOptions[] = $opActive;


            $arElFields = CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "LIST_SETTINGS")['VALUES'];
            $opElFields = array();
            $opElFields['ID'] = "iblock.ELEMENTFIELDS." . $iblock['ID'];
            $opElFields['NAME'] = "Поля элементов";
            $opElFields['TYPE'] = "multiselect";
            $opElFields['SIZE'] = 10;
            $opElFields['DEFAULT'] = "";
            $opElFields['VALUES'] = $arElFields;
            $arIBlockOptions[] = $opElFields;
            print_r($opElFields['DEFAULT']);

            $arElProperties = array();
            $rsProp = CIBlockProperty::GetList(
                Array("sort" => "asc", "name" => "asc"),
                Array("ACTIVE" => "Y", "IBLOCK_ID" => $iblock["ID"])
            );
            while ($arr = $rsProp->Fetch()) {
                if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S", "E"))) {
                    $arElProperties[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
                }
            }

            $arSecFields = array();

            $arSecProperties = array();
            foreach ($USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$iblock["ID"]."_SECTION") as $key => $value) {
                $arSecProperties[$key] = $key;
            }

            //\Bitrix\Main\Diag\Debug::dump($arSecProperties);
        }

    }



    $tabControl = new CAdminTabControl("tabControl", $arTabs);

    CModule::IncludeModule($module_id);

    if ($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && $RIGHT=="W" && check_bitrix_sessid()) {
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/prolog.php");

        foreach($arMainOptions as $arOption) {
            $name = $arOption['ID'];
            $val = (isset($_REQUEST[$name])) ? $_REQUEST[$name] : "";

            //COption::SetOptionString($module_id, $name, $val);
        }

        ob_start();
        $Update = $Update.$Apply;
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
        ob_end_clean();
    }

    ?>

    <form
        method="post"
        action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
        <?
        $tabControl->Begin();

        /*** MAIN ***/
        $tabControl->BeginNextTab();
        $tab = "main";

        foreach($arMainOptions as $arOption):
            if (is_array($arOption)):
                $val = COption::GetOptionString($module_id, $arOption['ID'], $arOption['DEFAULT']);
                $type = $arOption['TYPE'];
                $id = $tab . "." . $arOption['ID'];
                ?>
                <tr>
                    <td width="40%" nowrap <?=($type == "textarea") ? 'class="adm-detail-valign-top"' : ""?>>
                        <label for="<?=htmlspecialcharsbx($id)?>"><?= $arOption['NAME']?>:</label>
                    </td>
                    <td width="60%">
                        <? if($type == "checkbox"): ?>
                            <input id="<?=htmlspecialcharsbx($id)?>"
                                type="checkbox"
                                name="<?=htmlspecialcharsbx($id)?>"
                                value="Y" <?=($val == "Y") ? "checked" : ""?>>
                        <? elseif($type == "text"): ?>
                            <input id="<?= htmlspecialcharsbx($id)?>"
                                type="text"
                                size="<?=$arOption['SIZE']?>"
                                maxlength="255"
                                value="<?=htmlspecialcharsbx($val)?>"
                                name="<?=htmlspecialcharsbx($id)?>">
                        <? elseif($type == "textarea"): ?>
                            <textarea id="<?= htmlspecialcharsbx($id)?>"
                                rows="<?=$arOption['ROWS']?>"
                                cols="<?=$arOption['COLS']?>"
                                name="<?= htmlspecialcharsbx($id)?>">
                                <?= htmlspecialcharsbx($val)?></textarea>
                        <? endif; ?>
                    </td>
                </tr>
            <? else: ?>
                <tr class="heading">
                    <td colspan="2"><?=$arOption?></td>
                </tr>
            <? endif; ?>
        <? endforeach; ?>


        <!--Вкладка с инфоблоками-->
        <? if ($iBlockIncluded): ?>
            <?
                $tabControl->BeginNextTab();
                $arIblocksOptions = array();
            ?>
            <? foreach($arIBlockOptions as $arOption):
                if (is_array($arOption)): ?>
                    <?
                        $id = $arOption['ID'];
                        $type = $arOption['TYPE'];
                        $val = COption::GetOptionString($module_id, $id, $arOption['DEFAULT']);
                    ?>
                    <tr>
                        <td width="40%" nowrap <?=($type == "textarea") ? 'class="adm-detail-valign-top"' : ""?>>
                            <label for="<?=htmlspecialcharsbx($id)?>"><?=$arOption['NAME']?>:</label>
                        </td>
                        <td width="60%">
                            <? if($type == "checkbox"): ?>
                                <input
                                    type="checkbox"
                                    name="<?=htmlspecialcharsbx($id)?>"
                                    id="<?=htmlspecialcharsbx($id)?>"
                                    value="Y" <?=($val == "Y") ? "checked" : ""?>>
                            <? elseif($type == "text"): ?>
                                <input
                                    type="text"
                                    size="<?=$arOption['SIZE']?>"
                                    maxlength="255"
                                    value="<?=htmlspecialcharsbx($val)?>"
                                    name="<?=htmlspecialcharsbx($id)?>"
                                    id="<?=htmlspecialcharsbx($id)?>">
                            <? elseif($type == "textarea"): ?>
                                <textarea
                                    rows="<?=$arOption['ROWS']?>"
                                    cols="<?=$arOption['COLS']?>"
                                    name="<?=htmlspecialcharsbx($id)?>"
                                    id="<?=htmlspecialcharsbx($id)?>"><?= htmlspecialcharsbx($val)?></textarea>
                            <? elseif($type == "multiselect"): ?>
                                <select
                                    name="<?=htmlspecialcharsbx($id)?>[]"
                                    id="<?=htmlspecialcharsbx($id)?>"
                                    <?=($arOption['SIZE']) ? "size={$arOption['SIZE']}" : ""?>
                                    multiple>
                                    <?
                                    if ($val)
                                        $arSelectedValues = explode(";;;", $val);
                                    else
                                        $arSelectedValues = array();
                                    ?>
                                    <option <?=(count($arSelectedValues) > 0) ? "" : "selected"?>>(не выбрано)</option>
                                    <? foreach ($arOption['VALUES'] as $value => $name): ?>
                                        <option
                                            value="<?=$value?>"
                                            <?=(in_array($value, $arSelectedValues)) ? "selected" : ""?>>
                                            <?=$name?></option>
                                    <? endforeach; ?>
                                </select>
                            <? endif; ?>
                        </td>
                    </tr>
                <? else: ?>
                    <tr class="heading">
                        <td colspan="2"><?=$arOption?></td>
                    </tr>
                <? endif; ?>
            <? endforeach; ?>
        <? endif; ?>
    <? $tabControl->BeginNextTab();?>
    <? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
    <? $tabControl->Buttons();?>
    <input <?=($RIGHT < "W") ? "disabled" : ""?>
        type="submit"
        name="Update"
        value="<?=GetMessage("MAIN_SAVE")?>"
        title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>"
        class="adm-btn-save">
    <input <?=($RIGHT < "W") ? "disabled" : ""?>
        type="submit"
        name="Apply"
        value="<?=GetMessage("MAIN_OPT_APPLY")?>"
        title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
    <? if(strlen($_REQUEST["back_url_settings"])>0):?>
        <input <?=($RIGHT < "W") ? "disabled" : ""?>
            type="button"
            name="Cancel"
            value="<?=GetMessage("MAIN_OPT_CANCEL")?>"
            title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>"
            onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
        <input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
    <? endif; ?>
    <input
        type="submit"
        name="RestoreDefaults"
        title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>"
        OnClick="confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')"
        value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
    <?=bitrix_sessid_post();?>
    <? $tabControl->End();?>
    </form>
<?endif;?>
