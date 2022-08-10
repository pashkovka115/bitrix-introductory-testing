<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;


$arComponentParameters = [
    'GROUPS' => [
        'VARIABLES' => [
            'NAME' => Loc::getMessage('YLAB_TESTING_GROUPS'),
            "SORT" => 100
        ],
    ],
    'PARAMETERS' => [
        'IBLOCK_ID' => [
            'PARENT' => 'VARIABLES',
            'NAME' => Loc::getMessage('YLAB_TESTING_IBLOCK_ID'),
            'TYPE' => 'STRING',
            'DEFAULT' => '={$_REQUEST["ELEMENT_ID"]}',
            'VARIABLES' => ['ELEMENT_ID']
        ],
        'QUANTITY_QUESTIONS' => [
            'PARENT' => 'VARIABLES',
            'NAME' => Loc::getMessage('YLAB_TESTING_QUANTITY_QUESTIONS'),
            'TYPE' => 'STRING',
            'DEFAULT' => '={$_REQUEST["ELEMENT_ID"]}',
            'VARIABLES' => ['ELEMENT_ID']
        ],
        'CACHE_TIME' => ['DEFAULT' => 86400],
    ]
];
