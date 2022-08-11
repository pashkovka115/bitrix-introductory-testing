<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Новая страница");
?><?$APPLICATION->IncludeComponent(
	"ylab:interview.write",
	"",
	Array(
		"END_DAY" => "3",
		"IBLOCK_ID" => "16",
		"IBLOCK_TYPE" => "users",
		"SLOT_DATETIME" => "SLOT_DATETIME",
		"TIME_SLOT" => "30"
	)
);?><?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>