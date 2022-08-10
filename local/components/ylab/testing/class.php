<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Context;
\Bitrix\Main\Loader::includeModule('iblock');

class YlabTestingComponent extends CBitrixComponent{

    /**
     * @return string
     * Получение namespace инфоблока
     */

    public function getIblock(){
        return \Bitrix\Iblock\Iblock::wakeUp($this->arParams["IBLOCK_ID"])->getEntityDataClass();
    }

    /**
     * @return array
     * Получение массива случайных ID вопросов
     */

    public function getRandomId(){
        $iblock = $this->getIblock();
        $idElements = $iblock::getList([
            'select' => ['ID'],
            'runtime' => array('RAND'=>array('data_type' => 'float', 'expression' => array('RAND()'))),
            'order' => array('RAND'=>'ASC'),
            'limit' => $this->arParams["QUANTITY_QUESTIONS"],
        ]);
        $randomId = [];
        while ($idElement = $idElements->fetch())
        {
            array_push($randomId, $idElement["ID"]);
        }
        return $randomId;
    }
    /**
     * @param  array
     * Наполнение массива вопросами
     */

    public function getListQuestion(array $listId){
        $iblock = $this->getIblock();
        $typeQuestion = [];
        $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>"5",
            "CODE"=>"TYPE_QUESTION"));
        while($enum_fields = $property_enums->GetNext())
        {
            $typeQuestion[$enum_fields["ID"]] = $enum_fields["VALUE"];
        }
        foreach ($listId as $items){
            $element = $iblock::getList([
                'select' => ["ID", "NAME", "ANSWER", "TRUE_ANSWER", "TYPE_QUESTION"],
                'filter' => ['=ID' => $items]
            ])->fetchAll();
            foreach ($element as $item){
                $this->arResult["QUESTION_LIST"][$item["ID"]]["NAME"] = $item["NAME"];
                $this->arResult["QUESTION_LIST"][$item["ID"]]["TYPE_QUESTION"] =
                    $typeQuestion[$item["IBLOCK_ELEMENTS_ELEMENT_YLAB_INTERVIEW_QUESTIONS_TYPE_QUESTION_VALUE"]];
                if(is_null($this->arResult["QUESTION_LIST"][$item["ID"]]["ANSWER"])){
                    $this->arResult["QUESTION_LIST"][$item["ID"]]["ANSWER"] = [];
                }
                array_push($this->arResult["QUESTION_LIST"][$item["ID"]]["ANSWER"],
                    $item["IBLOCK_ELEMENTS_ELEMENT_YLAB_INTERVIEW_QUESTIONS_ANSWER_VALUE"]);
                $this->arResult["QUESTION_LIST"][$item["ID"]]["TRUE_ANSWER"] =
                    $item["IBLOCK_ELEMENTS_ELEMENT_YLAB_INTERVIEW_QUESTIONS_TRUE_ANSWER_VALUE"];
            }
        }
    }

    /**
     * @param  array
     * Разбор данных пришедших POST-запросом
     */

    public function getRequest(){
        $request = Context::getCurrent()->getRequest()->getPostList()->toArray();
        if (empty($request) or $request["newQuestions"] == 'y'){
            $this->arResult["NEW_QUESTIONS"] = 'n';
            $this->getListQuestion($this->getRandomId());
        }
        else {
            $this->arResult["NEW_QUESTIONS"] = 'y';
            $listId = [];
            foreach ($request as $key => $val) {
                array_push($listId, $key);
            }
            $this->getListQuestion($listId);
            $countTrueAnswer = 0;
            foreach ($request as $key => $val) {
                if ($this->arResult["QUESTION_LIST"][$key]["TRUE_ANSWER"] == $val){
                    $countTrueAnswer += 1;
                }
            }
            $this->arResult["COUNT_TRUE_ANSWER"] = $countTrueAnswer;
        };
    }

    public function executeComponent()
    {
        $this->getRequest();
        $this->includeComponentTemplate();
    }
}

