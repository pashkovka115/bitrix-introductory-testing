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

    $arAllActiveIbItems = \Bitrix\Iblock\ElementTable::getList([
        'select' => ['ID', 'NAME'],
        'filter' => [
            'IBLOCK_ID' => $iblockId,
            'ACTIVE' => true,
        ]
    ])->fetchAll();


    $arCompletedL = [];
    $arUncompleted = [];
    foreach ($hlData as $hlDatum) {
        foreach ($arAllActiveIbItems as $key => $ibItem) {
            if (!array_diff($hlDatum, $ibItem)['UF_LESSON_ID']) {
                if ($hlDatum['UF_COMPLETE'] == '1') {
                    $arCompletedL[$key] = $ibItem;
                    $arCompletedL[$key]['IS_COMPLETED'] = true;
                } else {
                    $arUncompleted[$key] = $ibItem;
                    $arUncompleted[$key]['IS_COMPLETED'] = false;
                }
            }
        }
    }
    $arSortedLessons = array_merge($arUncompleted, $arCompletedL);

    CIBlockElement::GetPropertyValuesArray($arAllLessonsImages, $iblockId, [], ['CODE' => 'IMAGE']);

    foreach ($arSortedLessons as $kLesson => $lesson) {
        foreach ($arAllLessonsImages as $kImage => $image) {
            if ($lesson['ID'] == $kImage) {
                $arSortedLessons[$kLesson]['IMAGE'] = $image['IMAGE']['VALUE'];
            }
        }
    }

    $arResult['LESSONS'] = array_merge($arSortedLessons);
}
$this->IncludeComponentTemplate($componentPage);
