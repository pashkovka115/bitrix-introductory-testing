<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

if(!CModule::IncludeModule('iblock'))
    return;

$arTypesEx = CIBlockParameters::GetIBlockTypes();

$arIBlocks = [];
$db_iblock = CIBlock::GetList(['SORT'=>'ASC'], ['SITE_ID'=>$_REQUEST['site'], 'TYPE' => ($arCurrentValues['IBLOCK_TYPE']!='-'?$arCurrentValues['IBLOCK_TYPE']:'')]);
while($arRes = $db_iblock->Fetch()) {
    $arIBlocks[$arRes['ID']] = '[' . $arRes['ID'] . '] ' . $arRes['NAME'];
}





$arComponentParameters = [
    'GROUPS' => [
        'IBLOCKS' => [
            'NAME' => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_GROUP_GENERAL'),
            'SORT' => 100
        ],
        'SLOTS' => [
            'NAME' => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_GROUP_SLOTS'),
            'SORT' => 200
        ],
        'SETTINGS' => [
            'NAME' => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_GROUP_DOP_SETTINGS'),
            'SORT' => 200
        ],
    ],
    'PARAMETERS' => [
        'IBLOCK_TYPE'  =>  [
            'PARENT' => 'IBLOCKS',
            'NAME' => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arTypesEx,
            'DEFAULT' => 'news',
            'REFRESH' => 'Y',
        ],
        'IBLOCK_ID'  =>  [
            'PARENT' => 'IBLOCKS',
            'NAME' => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks,
            'DEFAULT' => '',
            'MULTIPLE' => 'N',
        ],
        'TIME_SLOT' => [
            'PARENT' => 'SLOTS',
            'NAME' => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_TIME_SLOT'),
            'TYPE' => 'STRING',
            'DEFAULT' => '30',
        ],
        'END_DAY' => [
            'PARENT' => 'SLOTS',
            'NAME' => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_END_DAY'),
            'TYPE' => 'STRING',
            'DEFAULT' => '2',
        ],
        'SLOT_DATETIME' => [
            'PARENT' => 'SETTINGS',
            'NAME' => Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_SLOT_DATETIME'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'SLOT_DATETIME',
        ],
    ]
];

