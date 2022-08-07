<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

//формирование массива параметров
$arComponentParameters = array(
  "GROUPS" => array(
    "OPTIONS" => array(
      "NAME" => Loc::getMessage('YLAB_POSITION_IMPORT_OPTIONS_NAME'),
      "SORT" => "300",
    ),
  ),
  'PARAMETERS' => array(
    "POSITIONS_HL_NAME"    =>  array(
      "PARENT"    =>  "OPTIONS",
      "NAME"      =>  Loc::getMessage('YLAB_POSITION_IMPORT_PARAMETERS_POSITIONS_HL_NAME'),
      "TYPE"      =>  "STRING",
      "DEFAULT"   =>  "Positions"
    ),
    "ORGANIZATIONS_HL_NAME"    =>  array(
      "PARENT"    =>  "OPTIONS",
      "NAME"      =>  Loc::getMessage('YLAB_POSITION_IMPORT_PARAMETERS_ORGANIZATIONS_HL_NAME'),
      "TYPE"      =>  "STRING",
      "DEFAULT"   =>  "Organizations"
    ),
  ),
);
?>




