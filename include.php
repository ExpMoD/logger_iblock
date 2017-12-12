<?php

Bitrix\Main\Loader::registerAutoloadClasses(
    "logger_iblock",
    array(
        "logger_iblock\\HLB" => "lib/HLB.php",
        "logger_iblock\\Options" => "lib/Options.php",
    )
);


$arClasses = array(
    'AddEvents' => 'classes/AddEvents.php',
    'EditEvents' => 'classes/EditEvents.php',
    'DeleteEvents' => 'classes/DeleteEvents.php',
);

CModule::AddAutoloadClasses("logger_iblock", $arClasses);




CModule::IncludeModule("logger_iblock");