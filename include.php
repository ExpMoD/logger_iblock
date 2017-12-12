<?php
CModule::IncludeModule("logger_iblock");


$arClasses = array(
    'AddEvents' => 'classes/AddEvents.php',
    'EditEvents' => 'classes/EditEvents.php',
    'DeleteEvents' => 'classes/DeleteEvents.php',
);

CModule::AddAutoloadClasses("logger_iblock", $arClasses);