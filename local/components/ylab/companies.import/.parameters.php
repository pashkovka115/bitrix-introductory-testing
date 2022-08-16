<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

//формирование массива параметров
$arComponentParameters = array(
  "GROUPS" => array(
    "OPTIONS" => array(
      "NAME" => Loc::getMessage('YLAB_COMPANIES_IMPORT_OPTIONS_NAME'),
      "SORT" => "300",
    ),
  ),
  "ORGANIZATIONS_HL_NAME" => array(
    "PARENT" => "OPTIONS",
    "NAME" => Loc::getMessage('YLAB_COMPANIES_IMPORT_PARAMETERS_ORGANIZATIONS_HL_NAME'),
    "TYPE" => "STRING",
    "DEFAULT" => "Organizations"
  ),
);
?>




