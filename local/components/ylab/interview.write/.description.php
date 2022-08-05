<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    "NAME" => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_WRITE_TO_TESTING'),
    "DESCRIPTION" => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_WRITE_TO_TESTING_DESC'),
    "COMPLEX" => "N",
    "PATH" => [
        "ID" => 'ylab_local',
        "NAME" => 'Ylab',
    ],
];
