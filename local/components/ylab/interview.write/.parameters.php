<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

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
            'NAME' => 'Источник',
            'SORT' => 100
        ],
        'SLOTS' => [
            'NAME' => 'Слоты',
            'SORT' => 200
        ],
        'SETTINGS' => [
            'NAME' => 'Дополнительные возможности',
            'SORT' => 200
        ],
    ],
    'PARAMETERS' => [
       /* 'ELEMENT_ID' => [
            'PARENT' => 'VARIABLES',
            'NAME' => 'Тест переменная',
            'TYPE' => 'STRING',
            'DEFAULT' => '={$_REQUEST['ELEMENT_ID']}',
            'VARIABLES' => ['ELEMENT_ID']
        ],*/
        'IBLOCK_TYPE'  =>  [
            'PARENT' => 'IBLOCKS',
            'NAME' => 'Тип информационного блока (участники)',
            'TYPE' => 'LIST',
            'VALUES' => $arTypesEx,
            'DEFAULT' => 'news',
            'REFRESH' => 'Y',
        ],
        'IBLOCK_ID'  =>  [
            'PARENT' => 'IBLOCKS',
            'NAME' => 'Код информационного блока (участники)',
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks,
            'DEFAULT' => '',
            'MULTIPLE' => 'N',
        ],
        'TIME_SLOT' => [
            'PARENT' => 'SLOTS',
            'NAME' => 'Время слота в минутах',
            'TYPE' => 'STRING',
            'DEFAULT' => '30',
        ],
        'END_DAY' => [
            'PARENT' => 'SLOTS',
            'NAME' => 'Расчитывать слоты на столько дней',
            'TYPE' => 'STRING',
            'DEFAULT' => '2',
        ],
        'SLOT_DATETIME' => [
            'PARENT' => 'SETTINGS',
            'NAME' => 'Имя свойства для временного слота типа "Дата/Время"',
            'TYPE' => 'STRING',
            'DEFAULT' => 'SLOT_DATETIME',
        ],
    ]
];

