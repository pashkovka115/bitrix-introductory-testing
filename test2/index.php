<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
$APPLICATION->SetTitle("Страница для тестирования");
?><?$APPLICATION->IncludeComponent(
	"ylab:positions.list",
	"grid",
	Array(
		"ORGANIZATIONS_HL_NAME" => "Organizations",
		"POSITIONS_HL_NAME" => "Positions"
	)
);?><?$APPLICATION->IncludeComponent(
	"ylab:positions.import",
	"",
	Array(
		"ORGANIZATIONS_HL_NAME" => "Organizations",
		"POSITIONS_HL_NAME" => "Positions"
	)
);?><?php require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'; ?>