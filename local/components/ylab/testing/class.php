<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Context;

class YlabTestingComponent extends CBitrixComponent{
    private $quantRandomInt = 2;
    public function getAnswerList(){
        \Bitrix\Main\Loader::includeModule('iblock');
        $test = \Bitrix\Iblock\Iblock::wakeUp("5")->getEntityDataClass();
        $products = $test::getList([
            'select' => ['ID', 'NAME', 'ANSWER', 'TRUE_ANSWER'],
        ])->fetchAll();
        foreach ($products as $product) {
            $listResult["LIST"][$product["ID"]]["NAME"] = $product["NAME"];
            if(is_null($listResult["LIST"][$product["ID"]]["ANSWER"])){
                $listResult["LIST"][$product["ID"]]["ANSWER"] = [];
            }
            array_push($listResult["LIST"][$product["ID"]]["ANSWER"], $product["IBLOCK_ELEMENTS_ELEMENT_QUESTIONS_ANSWER_VALUE"]);
            $listResult["LIST"][$product["ID"]]["TRUE_ANSWER"] = $product["IBLOCK_ELEMENTS_ELEMENT_QUESTIONS_TRUE_ANSWER_VALUE"];
        }
        $randomList = array_rand($listResult["LIST"], $this->quantRandomInt);
        for ($i = 0; $i<$this->quantRandomInt; $i += 1) {
            $this->arResult["RANDOM_LIST"][$randomList[$i]] = $listResult["LIST"][$randomList[$i]];

        }
    }
    public function getAnswerUserList(){
        $request = Context::getCurrent()->getRequest()->getPostList()->toArray();
        if (empty($request)){
            $this->arResult["RESULTING_PAGE"] = "n";
        }
        else{
            $countTrueAnswer = 0;
            foreach ($request as $key => $val) {
                if ($this->arResult["RANDOM_LIST"][$key]["TRUE_ANSWER"] == $val){
                    $countTrueAnswer += 1;
                }
            }
            $this->arResult["RESULTING_PAGE"] = "y";
            $this->arResult["COUNT_TRUE_ANSWER"] = $countTrueAnswer;
        }
    }
    public function redirect(){
        $request = Context::getCurrent()->getRequest()->getPostList()->toArray();
        if ($request["redirect"] == "y"){
            $this->arResult["RESULTING_PAGE"] = "n";
            header('Location: '.$_SERVER['PHP_SELF']);
        }
    }
    public function executeComponent()
    {
        $this->redirect();
        $this->getAnswerList();
        $this->getAnswerUserList();
        $this->getAnswerUserList();
        $this->includeComponentTemplate();
    }
}

