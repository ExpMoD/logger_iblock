<?php
CModule::IncludeModule("logger_iblock");


$arClasses = array(
    'AddEvents' => 'classes/iblocks/AddEvents.php',
    'EditEvents' => 'classes/iblocks/EditEvents.php',
    'DeleteEvents' => 'classes/iblocks/DeleteEvents.php',
);

CModule::AddAutoloadClasses("logger_iblock", $arClasses);