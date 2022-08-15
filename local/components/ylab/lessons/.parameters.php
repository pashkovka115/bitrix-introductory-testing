<?


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock"))
    return;

use Bitrix\Main\Localization\Loc;

$arComponentParameters = array(
    "GROUPS" => array(),
    "PARAMETERS" => array(
        "VARIABLE_ALIASES" => array(
            "ELEMENT_ID" => array("NAME" => Loc::getMessage("NEWS_ELEMENT_ID_DESC")),
        ),
        "SEF_MODE" => array(
            "list" => array(
                "NAME" => Loc::getMessage("T_IBLOCK_SEF_PAGE_LIST"),
                "DEFAULT" => "",
                "VARIABLES" => array("ELEMENT_ID", "SECTION_ID"),
            ),
            "view" => array(
                "NAME" => Loc::getMessage("T_IBLOCK_SEF_PAGE_DETAIL"),
                "DEFAULT" => "view/#LESSON_ID#/",
                "VARIABLES" => array("ELEMENT_ID", "SECTION_ID"),
            ),
            "edit" => array(
                "NAME" => Loc::getMessage("T_IBLOCK_SEF_PAGE_EDIT"),
                "DEFAULT" => "edit/#LESSON_ID#/",
                "VARIABLES" => array("ELEMENT_ID", "SECTION_ID"),
            ),
        ),
        "IBLOCK_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("BN_P_IBLOCK"),
            "TYPE" => "STRING",
        ),
        "HLBLOCK_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("BN_P_HLBLOCK"),
            "TYPE" => "STRING",
        ),
        "SORT_BY1" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBORD1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ACTIVE_FROM",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER1" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("T_IBLOCK_DESC_IBBY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "CACHE_TIME" => array("DEFAULT" => 36000000),

    ),
);