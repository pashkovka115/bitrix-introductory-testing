<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use CIBlockElement;


class YlabInterviewUserAddForm extends \CBitrixComponent
{

    public function executeComponent()
    {
        if (Loader::includeModule('iblock')) {
            if ($_POST && $this::checkFields($_POST)) {
                $date = new DateTime();
                $login = $date->getTimestamp();
                if ($iblock_id = $this::checkLogin($login)) {
                    $password = $this::generatePassword(8, "0123456789");
                    $PROPERTY_VALUES = array();
                    $el = new CIBlockElement;
                    $PROPERTY_VALUES = [
                        "NAME" => $_POST["NAME"],
                        "SURNAME" => $_POST["SURNAME"],
                        "LASTNAME" => $_POST["LASTNAME"],
                        "POST" => $_POST["POST"],
                        "COMPANY" => $_POST["COMPANY"],
                        "PASSPORT" => $_POST["PASSPORT"],
                        "LOGIN" => $login,
                        "PASSWORD" => $password
                    ];
                    $userFieldsArray = [
                        "NAME" => $login,
                        "IBLOCK_ID" => $iblock_id,
                        "PROPERTY_VALUES" => $PROPERTY_VALUES
                    ];
                    if ($PRODUCT_ID = $el->Add($userFieldsArray))
                        header("Location: " . $_SERVER['REQUEST_URI'] . "?add=success");
                    else
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
                return false;
            }
        }
        return true;
    }

    public function checkLogin($login)
    {
        $iblock = CIBlockElement::GetList([], [
            'IBLOCK_CODE' => 'users'
        ],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_LOGIN']);

        while ($ob = $iblock->GetNextElement()) {
            $users = $ob->GetFields();
            if ($users["PROPERTY_LOGIN_VALUE"] == $login) {
                return false;
            }
        }

        return $users["IBLOCK_ID"];
    }

    public function generatePassword($length, $chars)
    {
        return substr(str_shuffle($chars), 0, $length);
    }
}
