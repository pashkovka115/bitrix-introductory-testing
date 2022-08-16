<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
$APPLICATION->SetTitle("Страница для тестирования");
?>

<? $APPLICATION->IncludeComponent(
	"ylab:positions.list",
	"grid",
	Array(
		"ORGANIZATIONS_HL_NAME" => "Organizations",
		"POSITIONS_HL_NAME" => "Positions"
	)
);?>

<? $APPLICATION->IncludeComponent(
  "ylab:companies.import",
  "",
  array(
    "ORGANIZATIONS_HL_NAME" => "Organizations",
    'AJAX_MODE' => 'Y',
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "Y",
    "AJAX_OPTION_HISTORY" => "N",
    "AJAX_OPTION_ADDITIONAL" => $arResult['COMPONENT_ID'],
  ),
  $component1
); ?>

<? $APPLICATION->IncludeComponent(
  "ylab:positions.import",
  "",
  array(
    "ORGANIZATIONS_HL_NAME" => "Organizations",
    "POSITIONS_HL_NAME" => "Positions",
    'AJAX_MODE' => 'Y',
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "Y",
    "AJAX_OPTION_HISTORY" => "N",
    "AJAX_OPTION_ADDITIONAL" => $arResult['COMPONENT_ID'],
  ),
  $component2
); ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>