# Список элементов и категорий инфоблока на D7

Пример вызова:

~~~
$APPLICATION->IncludeComponent(
    "antipow:iblock.list",
    "",
    [
        "CACHE_TIME"          => "86000",
        "CACHE_TYPE"          => "A",
        "FIELD_CODE"          => ["ID", "CODE", "IBLOCK_SECTION_ID", "XML_ID", "NAME", "SORT", ""],
        "IBLOCK_ID"           => "17",
        "PROPERTY_SINGLE_ID"  => [1, 2, 3, 4],
        "SORT_BY"             => "ID",
        "SORT_ORDER"          => "ASC",
        "ELEMENT_COUNT"       => 15
    ]
);
~~~
