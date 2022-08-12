<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

global $USER;

Loader::includeModule("highloadblock");
Loader::includeModule("iblock");

$iblockId = \Bitrix\Iblock\IblockTable::getList(['filter' => ['CODE' => 'YLAB_LESSONS']])->Fetch()["ID"];

$hlbl = Hl\HighloadBlockTable::getList(['filter' => ['NAME' => 'ProgressPerLessons']])->fetch()['ID'];

$arDefaultUrlTemplates404 = array(
    'list' => '',
    'view' => 'LESSON_ID#/',
    'edit' => '',
);

$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();

$arComponentVariables = array(
    'LESSON_ID',
    'EDIT_CODE'
);
if ($arParams["SEF_MODE"] == "Y") {
    $arVariables = array();

    $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams['SEF_URL_TEMPLATES']);

    $arVariableAliases = CComponentEngine::makeComponentVariableAliases($arDefaultUrlTemplates404, $arParams['VARIABLE_ALIASES']);

    $componentPage = CComponentEngine::parseComponentPath($arParams['SEF_FOLDER'], $arUrlTemplates, $arVariables);
    if (!$componentPage) {
        $componentPage = 'list';
    }
    CComponentEngine::initComponentVariables($componentPage, $arComponentVariables, $arDefaultVariableAliases, $arVariables);
}
if ($componentPage == 'view') {
    $LessonID = $arVariables['ELEMENT_ID'];
    $arLessonData = \Bitrix\Iblock\ElementTable::getList([
        'select' => ['ID', 'NAME'],
        'filter' => [
            'IBLOCK_ID' => $iblockId,
            'ID' => $LessonID,
        ]
    ])->fetch();

    $propertiesDB = CIBlockElement::GetProperty($iblockId, $LessonID);
    while ($property = $propertiesDB->GetNext()) {
        $arLessonData[$property['CODE']] = $property['VALUE'];
    }

    $arResult['LESSON_DATA'] = $arLessonData;
}

if ($componentPage == 'edit') {
    // оставлено до стыковки с кэшированными вопросами/ответами
}

if ($componentPage == 'list') {

    $userID = $USER->GetID();

    $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();

    $rsData = $entity_data_class::getList(array(
        "select" => [
            'UF_LESSON_ID',
            'UF_COMPLETE',
        ],
        "filter" => array("UF_USER_ID" => $userID),

    ));

    $hlData = $rsData->fetchAll();

    $completedLessons = [];

    foreach ($hlData as $hlDatum) {
        if ($hlDatum['UF_COMPLETE'] == 1) {
            $completedLessons[] = $hlDatum['UF_LESSON_ID'];
        }
    }

    foreach ($hlData as $hlDatum) {
        $arIbItems[] = \Bitrix\Iblock\ElementTable::getList([
            'select' => ['ID', 'NAME'],
            'filter' => [
                'IBLOCK_ID' => $iblockId,
                'ID' => $hlDatum['UF_LESSON_ID']
            ]
        ])->fetch();
    }

    foreach ($arIbItems as $key => $arIbItem) {
        foreach ($completedLessons as $completedLesson) {
            if ($arIbItem['ID'] == $completedLesson) {
                $arIbItems[$key]['IS_COMPLETED'] = true;
            }
        }
    }
    foreach ($arIbItems as $key => $arIbItem) {
        if (!$arIbItems[$key]['IS_COMPLETED']) {
            $arIbItems[$key]['IS_COMPLETED'] = false;
        }
    }

    foreach ($arIbItems as $key => $arIbItem) {
        $propertiesDB = CIBlockElement::GetProperty($iblockId, $arIbItem['ID']);
        while ($property = $propertiesDB->GetNext()) {
            if ($property['CODE'] == 'IMAGE') {
                $arIbItems[$key][$property['CODE']] = $property['VALUE'];
            }
        }
    }

    $arIbItemsSortCompleted = [];
    $arIbItemsSortUnCompleted = [];
    foreach ($arIbItems as $arIbItem) {
        if ($arIbItem['IS_COMPLETED']) {
            $arIbItemsSortCompleted[] = $arIbItem;
        } else {
            $arIbItemsSortUnCompleted[] = $arIbItem;
        }
    }

    $arResult['LESSONS'] = array_merge($arIbItemsSortUnCompleted, $arIbItemsSortCompleted);
}
$this->IncludeComponentTemplate($componentPage);
