<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Добавление пользователей");
?><?$APPLICATION->IncludeComponent(
    "ylab:interview.user.add",
    "",
    Array(
    )
);?><?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>