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
        array("ID" => "ENABLED", "NAME" => GetMessage("LIB_OPTIONS_ENABLED"), "TYPE" => "checkbox", 'SIZE' => false, "DEFAULT" => "Y"),
    );

    $arIBlockOptions = array();

    if ($iBlockIncluded) {
        $arIBlocks = CIBlock::GetList();

        while ($iblock = $arIBlocks->GetNext()) {
            $arIBlockOptions[] = $iblock['NAME'] /*. " ({$iblock['CODE']})"*/;


        }
    }



    $tabControl = new CAdminTabControl("tabControl", $arTabs);

    CModule::IncludeModule($module_id);

    if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && $RIGHT=="W" && check_bitrix_sessid())
    {
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/prolog.php");

        foreach($arMainOptions as $arOption) {
            $name = $arOption['ID'];
            $val = (isset($_REQUEST[$name])) ? $_REQUEST[$name] : "";

            COption::SetOptionString($module_id, $name, $val);
        }

        ob_start();
        $Update = $Update.$Apply;
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
        ob_end_clean();
    }

    ?>

    <form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=LANGUAGE_ID?>">
        <?
        $tabControl->Begin();
        $tabControl->BeginNextTab();

        foreach($arMainOptions as $arOption):
            if (is_array($arOption)):
                $val = COption::GetOptionString($module_id, $arOption['ID'], $arOption['DEFAULT']);
                $type = $arOption['TYPE'];
                ?>
                <tr>
                    <td width="40%" nowrap <?if($type == "textarea") echo 'class="adm-detail-valign-top"'?>>
                        <label for="<?echo htmlspecialcharsbx($arOption['ID'])?>"><?echo $arOption['NAME']?>:</label>
                    </td>
                    <td width="60%">
                        <? if($type == "checkbox"): ?>
                            <input type="checkbox" name="<?=htmlspecialcharsbx($arOption['ID'])?>" id="<?=htmlspecialcharsbx($arOption['ID'])?>" value="Y"<?if($val=="Y")echo" checked";?>>
                        <? elseif($type == "text"): ?>
                            <input type="text" size="<?=$arOption['SIZE']?>" maxlength="255" value="<?=htmlspecialcharsbx($val)?>" name="<?=htmlspecialcharsbx($arOption['ID'])?>" id="<?echo htmlspecialcharsbx($arOption['ID'])?>"><?if($arOption[0] == "slow_sql_time") echo GetMessage("PERFMON_OPTIONS_SLOW_SQL_TIME_SEC")?>
                        <? elseif($type == "textarea"): ?>
                            <textarea rows="<?=$arOption['ROWS']?>" cols="<?=$arOption['COLS']?>" name="<?echo htmlspecialcharsbx($arOption['ID'])?>" id="<?echo htmlspecialcharsbx($arOption['ID'])?>"><?echo htmlspecialcharsbx($val)?></textarea>
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
            <? $tabControl->BeginNextTab(); ?>
            <? foreach($arIBlockOptions as $arOption):
                if (is_array($arOption)): ?>
                    <?
                        $val = COption::GetOptionString($module_id, $arOption['ID'], $arOption['DEFAULT']);
                        $type = $arOption['TYPE'];
                    ?>
                    <tr>
                        <td width="40%" nowrap <?if($type == "textarea") echo 'class="adm-detail-valign-top"'?>>
                            <label for="<?echo htmlspecialcharsbx($arOption['ID'])?>"><?echo $arOption['NAME']?>:</label>
                        </td>
                        <td width="60%">
                            <? if($type == "checkbox"): ?>
                                <input type="checkbox" name="<?=htmlspecialcharsbx($arOption['ID'])?>" id="<?=htmlspecialcharsbx($arOption['ID'])?>" value="Y"<?if($val=="Y")echo" checked";?>>
                            <? elseif($type == "text"): ?>
                                <input type="text" size="<?=$arOption['SIZE']?>" maxlength="255" value="<?=htmlspecialcharsbx($val)?>" name="<?=htmlspecialcharsbx($arOption['ID'])?>" id="<?echo htmlspecialcharsbx($arOption['ID'])?>"><?if($arOption[0] == "slow_sql_time") echo GetMessage("PERFMON_OPTIONS_SLOW_SQL_TIME_SEC")?>
                            <? elseif($type == "textarea"): ?>
                                <textarea rows="<?=$arOption['ROWS']?>" cols="<?=$arOption['COLS']?>" name="<?echo htmlspecialcharsbx($arOption['ID'])?>" id="<?echo htmlspecialcharsbx($arOption['ID'])?>"><?echo htmlspecialcharsbx($val)?></textarea>
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
    <input <?if ($RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
    <input <?if ($RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
    <? if(strlen($_REQUEST["back_url_settings"])>0):?>
        <input <?if ($RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
        <input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
    <? endif; ?>
    <input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
    <?=bitrix_sessid_post();?>
    <? $tabControl->End();?>
    </form>
<?endif;?>
