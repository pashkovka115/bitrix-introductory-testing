<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use CIBlockElement;
use Bitrix\Main\Engine\Contract\Controllerable;


class YlabInterviewUserAddForm extends \CBitrixComponent implements Controllerable
{
    const IBLOCK_CODE = 'ylab_interview_users';

    public function configureActions()
    {

        return [
            'checkField' => [
                'prefilters' => [],
            ],
        ];
    }

    public function checkPassportAction($post)
    {
        $passport = $post['passport'];
        $iblock_id = $post['iblock_id'];
        $elements = [];
        $filter = ['IBLOCK_ID' => $iblock_id];
        CIBlockElement::GetPropertyValuesArray($elements, $filter['IBLOCK_ID'], $filter);
        unset($rows, $filter, $order);
        $data["correct"] = 1;
        $data["message"] = Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_PASSPORT_CORRECT');
        foreach ($elements as $element_id => $element_property) {
            if ($element_property["PASSPORT"]["~VALUE"] == $passport) {
                $data["correct"] = 0;
                $data["message"] = Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_PASSPORT_ERROR');
                return $data;
            }
        }

        return $data;
    }

    public function executeComponent()
    {
        if (Loader::includeModule('iblock')) {
            $request = Application::getInstance()->getContext()->getRequest();
            $input_date = $request->toArray();
            $iblock_id = $this->getIblockIdFromCode(self::IBLOCK_CODE);
            $this->arResult["iblock_id"] = $iblock_id;
            if ($request->isPost() && $this->checkFields($input_date)) {
                $date = new DateTime();
                $login = $date->getTimestamp();
                if ($this->checkLogin($login, $iblock_id)) {
                    $password = $this->generatePassword(8, "0123456789");
                    $property_values = array();
                    $el = new CIBlockElement;
                    $property_values = [
                        "NAME" => $input_date["NAME"],
                        "SURNAME" => $input_date["SURNAME"],
                        "LASTNAME" => $input_date["LASTNAME"],
                        "POST" => $input_date["POST"],
                        "COMPANY" => $input_date["COMPANY"],
                        "PASSPORT" => $input_date["PASSPORT"],
                        "LOGIN" => $login,
                        "PASSWORD" => md5($password)
                    ];
                    $userFieldsArray = [
                        "NAME" => $login,
                        "IBLOCK_ID" => $iblock_id,
                        "PROPERTY_VALUES" => $property_values
                    ];
                    if ($PRODUCT_ID = $el->Add($userFieldsArray)) {
                        $this->arResult['SUCCESS'] = Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_SUCCESS');
                        $this->arResult['LINK'] = $_SERVER['REQUEST_URI'];
                    } else
                        echo "Error: " . $el->LAST_ERROR;

                }

            }
            $this->includeComponentTemplate();
        }
    }

    public function checkFields($post)
    {
        foreach ($post as $field) {
            if (empty($field)) {
                $this->arResult["ERRORS"] = Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_ERROR_FIELDS');
                return false;
            }
        }
        return true;
    }

    public function getIblockIdFromCode($iblock_code)
    {
        $iblock = CIBlockElement::GetList([], [
            'IBLOCK_CODE' => $iblock_code
        ],
            false,
            false,
            ['IBLOCK_ID']);
        if ($ob = $iblock->GetNext()) {
            $iblock_id = $ob['IBLOCK_ID'];
        }
        return $iblock_id;
    }

    public function checkLogin($login, $iblock_id)
    {
        $elements = [];
        $filter = ['IBLOCK_ID' => $iblock_id];
        CIBlockElement::GetPropertyValuesArray($elements, $filter['IBLOCK_ID'], $filter);
        unset($rows, $filter, $order);
        foreach ($elements as $element_id => $element_property) {
            if ($element_property["LOGIN"]["~VALUE"] == $login) {
                return false;
            }
        }

        return true;
    }

    public function generatePassword($length, $chars)
    {
        return substr(str_shuffle($chars), 0, $length);
    }
}
