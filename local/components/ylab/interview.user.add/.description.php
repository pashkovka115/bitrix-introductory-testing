<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
    "NAME" => Loc::getMessage('YLAB_INTERVIEW_USER_ADD_FORM'),
    "DESCRIPTION" => Loc::getMessage('YLAB_INTERVIEW_USER_ADD_FORM_DESCRIPTION'),
    "COMPLEX" => "N",
    "PATH" => [
        "ID" => 'ylab_local',
        "NAME" => 'Ylab',
    ],
];