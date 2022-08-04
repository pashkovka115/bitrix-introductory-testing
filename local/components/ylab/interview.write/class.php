<?php

namespace Ylab\Interview\Write;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use CIBlockElement;


class YlabMeetingInterviewWrite extends \CBitrixComponent
{
    const FORMATE_DATE_TIME = 'd.m.Y H:i:s';
    /**
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     * Проверяет подключение модулей необходимых для работы этого компонента
     */
    private function checkModules()
    {
        return Loader::includeModule('iblock');
    }

    public function onPrepareComponentParams($params)
    {
        $this->arParams['IBLOCK_TYPE'] = $params['IBLOCK_TYPE'] ?? '';
        $this->arParams['IBLOCK_ID'] = $params['IBLOCK_ID'] ?? '';
        $this->arParams['TIME_SLOT'] = $params['TIME_SLOT'] ?? '';
        $this->arParams['END_DAY'] = $params['END_DAY'] ?? '';
        if (isset($params['SLOT_DATETIME']) && is_string($params['SLOT_DATETIME']) && strlen($params['SLOT_DATETIME']) > 0){



            $this->arParams['SLOT_DATETIME'] = $params['SLOT_DATETIME'];
        }else{
            throw new ArgumentNullException('SLOT_DATETIME (Имя поля для временного слота)');
        }

        return $params;
    }


    public function executeComponent()
    {
        if ($this->checkModules()){
            $request = Application::getInstance()->getContext()->getRequest();

            if ($request->isPost()){
                $this->handlerPost($request);
                LocalRedirect($this->app()->GetCurPage());
            }else{
                $this->handlerGet();
            }


            $this->includeComponentTemplate();
        }else{
            ShowMessage([
                'MESSAGE' => 'Не загрузились необходимые модули для работы компонента',
                'TYPE' => 'ERROR'
            ]);
        }
    }


    public function handlerGet()
    {

        $all_slots = $this->getTotalSlotsAsArray();
        $db_slots = $this->getDBSlots();
        $this->arResult['SLOTS'] = $this->getGeneratedSlots($all_slots, $db_slots);

        $this->arResult['USERS'] = $this->getUsers();

    }


    public function handlerPost(HttpRequest $request)
    {
        $input_data = $request->toArray();

        if (check_bitrix_sessid()){
            CIBlockElement::SetPropertyValueCode(
                $input_data['USER'], //  id пользователя из $_POST ($ELEMENT_ID)
                $this->arParams['SLOT_DATETIME'], // имя свойства из настроек компонента ($PROPERTY_CODE)
                $input_data['SLOT_DATETIME'] // новое значение (слот времени) ($PROPERTY_VALUE)
            );
        }

    }


    public function getUsers()
    {
        $iblock = CIBlockElement::GetList([], [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID']
        ],
            false,
            false,
            ['ID', 'NAME']);

        $users = [];
        while($ob = $iblock->GetNextElement())
        {
            $fields = $ob->GetFields();
            $users[] = [
                'ID' => $fields['ID'],
                'NAME' => $fields['NAME'],
            ];
        }

        return $users;
    }


    public function getGeneratedSlots($all_slots, $db_slots)
    {
        $view_slots = [];
        foreach ($all_slots as $day => $day_values){
            foreach ($day_values as $slot){
                foreach ($db_slots as $db_slot){

                    $db_slot['FREE'] = 'Y';
                    if ($slot == $db_slot['VALUE']){
                        $db_slot['FREE'] = 'N';
                        break;
                    }
                }

                $item = [
                    'ELEMENT_ID' => $db_slot['ELEMENT_ID'],
                    'NAME' => $db_slot['NAME'],
                    'SLOT' => ['VALUE' => $slot, 'FREE' => $db_slot['FREE']],
                ];
                $view_slots[$day][] = $item;

            }
        }

        return $view_slots;
    }


    public function getDBSlots()
    {
        $db_slots = [];

        $res = CIBlockElement::GetList([], [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID']
        ]);

        while($ob = $res->GetNextElement())
        {
            $fields = $ob->GetFields();
            $properties = $ob->GetProperties();
            $db_slots[] = [
                'NAME' => $fields['NAME'],
                'ELEMENT_ID' => $fields['ID'],
                'VALUE' => trim($properties[$this->arParams['SLOT_DATETIME']]['VALUE'])
            ];
        }

        return $db_slots;
    }


    public function getTotalSlotsAsArray()
    {
        $all_slots = [];
        foreach ($this->getTotalSlots() as $day => $value){
            /** @var DateTime $slot */
            foreach ($value as $slot){
                $all_slots[$day][] = trim($slot->format(self::FORMATE_DATE_TIME));
            }
        }

        return $all_slots;
    }


    /**
     * @return array
     * @throws \Bitrix\Main\ObjectException
     */
    public function getTotalSlots()
    {
        $all_slots = [];
        for ($i = 0; $i < $this->arParams['END_DAY'] * 1; $i++){
            if ($i > 0){
                $all_slots[(new DateTime())->add("$i days")->format('d.m.Y')] = $this->getTimeSlots((new DateTime())->add("$i days"));
            }else{
                // начиная с сегодня
                $all_slots[(new DateTime())->format('d.m.Y')] = $this->getTimeSlots(new DateTime());
            }
        }

        return $all_slots;
    }


    /**
     * @param DateTime $date
     * @return array
     * @throws \Bitrix\Main\ObjectException
     * Разбивает сутки на слоты за указанную дату
     */
    public function getTimeSlots(DateTime $date)
    {
        $all_slots = [];
        $all_slots[] = new DateTime($date->format('d.m.Y'));
        $total_slots = floor(1440 / $this->arParams['TIME_SLOT'] * 1);
        $start_day = (new DateTime($date->format('d.m.Y')));

        for ($i = 1; $i < $total_slots; $i++) {
            $all_slots[] = new DateTime($start_day->add(($this->arParams['TIME_SLOT'] * 1) . ' minutes'));
        }
        return $all_slots;
    }


    public function app()
    {
        global $APPLICATION;

        return $APPLICATION;
    }
}
