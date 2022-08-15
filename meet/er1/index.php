<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
?><?$APPLICATION->IncludeComponent(
	"ylab:testing",
	"",
	Array(
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"IBLOCK_ID" => "5",
		"IBLOCK_TYPE" => "questions",
        "QUANTITY_QUESTIONS" => '3'
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>