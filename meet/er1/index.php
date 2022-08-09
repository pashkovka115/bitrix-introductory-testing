<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?>

<?$APPLICATION->IncludeComponent(
	"ylab:testing",
	".default",
	Array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_ID" => "5",
		"IBLOCK_TYPE" => "questions"
	)
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>