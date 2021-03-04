<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//Инфоблоки
$arIBlocks = [];
$db_iblock = CIBlock::GetList(["SORT" => "ASC"], [
    "SITE_ID" => $_REQUEST["site"],
    "TYPE"    => ($arCurrentValues["IBLOCK_TYPE"] != "-" ? $arCurrentValues["IBLOCK_TYPE"] : "")
]);
while ($arRes = $db_iblock->Fetch()) {
    $arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
}

//Сортировка
$arSorts = ["ASC" => "По возрастания", "DESC" => "По убыванию"];
$arSortFields = [
    "ID"   => "ID",
    "NAME" => "Название",
    "SORT" => "Сортировка"
];


//Поля инфоблока


//Свойства инфоблока
$arProperty_LNS = [];
$rsProp = CIBlockProperty::GetList(["sort" => "asc", "name" => "asc"], [
    "ACTIVE"    => "Y",
    "IBLOCK_ID" => (isset($arCurrentValues["IBLOCK_ID"]) ? $arCurrentValues["IBLOCK_ID"] : $arCurrentValues["ID"])
]);
while ($arr = $rsProp->Fetch()) {
    $arProperty[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    if (in_array($arr["PROPERTY_TYPE"], ["L", "N", "S"])) {
        $arProperty_LNS[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
    }
}

//Параметры
$arComponentParameters = [
    "GROUPS"     => [
    ],
    "PARAMETERS" => [
        "IBLOCK_ID"     => [
            "PARENT"            => "BASE",
            "NAME"              => "ID инфоблока",
            "TYPE"              => "LIST",
            "VALUES"            => $arIBlocks,
            "DEFAULT"           => '',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH"           => "Y",
        ],
        "SORT_BY"       => [
            "PARENT"            => "DATA_SOURCE",
            "NAME"              => "Поле сортировки",
            "TYPE"              => "LIST",
            "DEFAULT"           => "ID",
            "VALUES"            => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "SORT_ORDER"    => [
            "PARENT"            => "DATA_SOURCE",
            "NAME"              => "Направление сортировки",
            "TYPE"              => "LIST",
            "DEFAULT"           => "ASC",
            "VALUES"            => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ],
        "FIELD_CODE"    => CIBlockParameters::GetFieldCode("Поля инфоблока", "DATA_SOURCE"),
        "PROPERTY_SINGLE_ID" => [
            "PARENT"            => "DATA_SOURCE",
            "NAME"              => "ID одиночного свойства",
            "TYPE"              => "LIST",
            "MULTIPLE"          => "Y",
        ],
        "ELEMENT_COUNT" => [
            "PARENT"  => "BASE",
            "NAME"    => "Кол-во элементов",
            "TYPE"    => "STRING",
            "DEFAULT" => "10",
        ],
        "CACHE_TIME"    => ["DEFAULT" => 86000],
    ],
];
