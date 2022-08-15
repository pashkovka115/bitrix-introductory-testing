<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

global $USER;

Loader::includeModule("highloadblock");
Loader::includeModule("iblock");

define("IBLOCK_CODE", $arParams['IBLOCK_CODE']);
define("HLBLOCK_CODE", $arParams['HLBLOCK_CODE']);

$iblockId = \Bitrix\Iblock\IblockTable::getList(['filter' => ['CODE' => IBLOCK_CODE]])->Fetch()["ID"];
$hlbl = Hl\HighloadBlockTable::getList(['filter' => ['NAME' => HLBLOCK_CODE]])->fetch()['ID'];

$hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

$userID = $USER->GetID();

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

    $lessonID = $arVariables['LESSON_ID'];

    $arLessonData = \Bitrix\Iblock\ElementTable::getList([
        'select' => ['ID', 'NAME'],
        'filter' => [
            'IBLOCK_ID' => $iblockId,
            'ID' => $lessonID,
        ]
    ])->fetch();

    $hlData = $entity_data_class::getList([
        "select" => [
            'UF_COMPLETE',
        ],
        "filter" => [
            'UF_USER_ID' => $userID,
            'UF_LESSON_ID' => $lessonID
        ],

    ])->fetch();

    $arLessonData['IS_COMPLETED'] = $hlData['UF_COMPLETE'] == '1';

    $propertiesDB = CIBlockElement::GetProperty($iblockId, $lessonID);
    while ($property = $propertiesDB->GetNext()) {
        $arLessonData[$property['CODE']] = $property['VALUE'];
    }

    $arResult['LESSON_DATA'] = $arLessonData;
}

if ($componentPage == 'edit') {

    $lessonID = $arVariables['LESSON_ID'];

    $arLessonData = \Bitrix\Iblock\ElementTable::getList([
        'select' => ['ID', 'NAME'],
        'filter' => [
            'IBLOCK_ID' => $iblockId,
            'ID' => $lessonID,
        ]
    ])->fetch();

    $hlData = $entity_data_class::getList([
        "select" => [
            'ID',
            'UF_COMPLETE',
        ],
        "filter" => [
            'UF_USER_ID' => $userID,
            'UF_LESSON_ID' => $lessonID
        ],

    ])->fetch();

    $arLessonData['IS_COMPLETED'] = $hlData['UF_COMPLETE'] == '1';
    $arResult['LESSON_DATA'] = $arLessonData;

    $request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
    if (($lessonID) && ($request->getPost('option'))) {
        $entity_data_class::update($hlData['ID'], ['UF_COMPLETE' => '1']);
    }
}

if ($componentPage == 'list') {

    $hlData = $entity_data_class::getList([
        "select" => [
            'UF_LESSON_ID',
            'UF_COMPLETE',
        ],
        "filter" => ['UF_USER_ID' => $userID],

    ])->fetchAll();
    $hlIdList = [];

    foreach ($hlData as $hlDatum) {
        $hlIdList[] = $hlDatum['UF_LESSON_ID'];
    }


    $arAllActiveIbItems = \Bitrix\Iblock\ElementTable::getList([
        'select' => ['ID', 'NAME'],
        'filter' => [
            'IBLOCK_ID' => $iblockId,
            'ACTIVE' => 'Y',
        ]
    ])->fetchAll();

    $ibIdList = [];
    foreach ($arAllActiveIbItems as $ibItem) {
        $ibIdList[] = $ibItem['ID'];
    }

    $cUserLessons = array_intersect($ibIdList, $hlIdList);

    $lessonsResult = [];
    foreach ($arAllActiveIbItems as $ibLesson) {
        foreach ($cUserLessons as $userLesson) {
            if ($ibLesson['ID'] == $userLesson) {
                $lessonData['ID'] = $ibLesson['ID'];
                $lessonData['NAME'] = $ibLesson['NAME'];
                $lessonsResult[] = $lessonData;
            }
        }
    }

    foreach ($lessonsResult as $key => $lesson) {
        foreach ($hlData as $hlDatum) {
            if ($lesson['ID'] == $hlDatum['UF_LESSON_ID']) {
                $lessonsResult[$key]['IS_COMPLETED'] = $hlDatum['UF_COMPLETE'] == 1;
            }
        }
    }

    CIBlockElement::GetPropertyValuesArray($arAllLessonsImages, $iblockId, [], ['CODE' => 'IMAGE']);

    foreach ($lessonsResult as $kLesson => $lesson) {
        foreach ($arAllLessonsImages as $kImage => $image) {
            if ($lesson['ID'] == $kImage) {
                $lessonsResult[$kLesson]['IMAGE'] = CFile::GetFileArray($image['IMAGE']['VALUE'])['SRC'];
            }
        }
    }

    $arResult['LESSONS'] = array_merge($lessonsResult);
}
$this->IncludeComponentTemplate($componentPage);
