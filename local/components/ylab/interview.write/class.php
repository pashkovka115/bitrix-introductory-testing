<?php

namespace Ylab\Interview\Write;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use CIBlockElement;
use Bitrix\Main\Engine\ActionFilter;


class YlabInterviewWrite extends \CBitrixComponent implements Controllerable
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
        $request = Application::getInstance()->getContext()->getRequest();

        if ($request->isAjaxRequest()) {
            return;
        }

        // символьный код свойства
        if (isset($params['SLOT_DATETIME']) && is_string($params['SLOT_DATETIME']) && strlen($params['SLOT_DATETIME']) > 0) {
            $this->arParams['SLOT_DATETIME'] = $params['SLOT_DATETIME'];
        } else {
            throw new ArgumentNullException('SLOT_DATETIME (Имя поля для временного слота)');
        }

        if (isset($params['IBLOCK_ID']) && (($params['IBLOCK_ID'] * 1) > 0)) {
            $this->arParams['IBLOCK_ID'] = intval($params['IBLOCK_ID']);
        } else {
            throw new ArgumentNullException('IBLOCK_ID (Идентификатор инфоблока)');
        }

        // продолжительность слота в минутах
        if (isset($params['TIME_SLOT']) && (($params['TIME_SLOT'] * 1) > 0)) {
            $this->arParams['TIME_SLOT'] = intval($params['TIME_SLOT']);
        } else {
            throw new ArgumentNullException('TIME_SLOT (Время слота)');
        }

        // на сколько дней генерировать слоты
        if (isset($params['END_DAY']) && (($params['END_DAY'] * 1) > 0)) {
            $this->arParams['END_DAY'] = intval($params['END_DAY']);
        } else {
            throw new ArgumentNullException('END_DAY (На сколько дней генерировать)');
        }

        return $params;
    }


    public function executeComponent()
    {
        $this->arResult['ERRORS'] = [];

        if ($this->checkModules()) {
            $this->handler();
            $this->includeComponentTemplate();
        } else {
            $this->arResult['ERRORS'][] = 'Не загрузились необходимые модули для работы компонента';
        }
    }


    /**
     * обработчик запросов не связанных с формой
     * обычно это GET
     */
    public function handler()
    {

        $all_slots = $this->getTotalSlotsAsArray();
        $db_slots = $this->getDBSlots();
        $this->arResult['SLOTS'] = $this->getGeneratedSlots($all_slots, $db_slots);

        $this->arResult['USERS'] = $this->getUsers();
    }


    /**
     * @param $slot_datetime - новое значение слота для юзера
     * @param $user_id - для этого юзера
     * @param $iblock_id - с каким инфоблоком работаем
     * @param string $slot_datetime_name - символьный код свойства
     * @param int $time_slot - продолжительность слота в минутах
     * @param int $end_day - на сколько дней генерировать слоты
     * @return
     */
    public function ajaxHandlerAction(
        $slot_datetime,
        $user_id,
        $iblock_id,
        $slot_datetime_name = 'LIST_SLOT_DATETIME',
        $time_slot = 30,
        $end_day = 2
    )
    {
        $this->arParams['IBLOCK_ID'] = $iblock_id;
        $this->arParams['SLOT_DATETIME'] = $slot_datetime_name;
        $this->arParams['TIME_SLOT'] = $time_slot;
        $this->arParams['END_DAY'] = $end_day;

        try {
            // если надо сохранить пустое значение необходимо передать false
            if ($slot_datetime == ''){
                $slot_datetime = false;
            }
            CIBlockElement::SetPropertyValuesEx(
                $user_id, //  id пользователя из $_POST ($ELEMENT_ID)
                $iblock_id,
                [$slot_datetime_name => $slot_datetime], // новое значение (слот времени) ($PROPERTY_VALUE)
                $slot_datetime_name, // имя свойства из настроек компонента ($PROPERTY_CODE)
            );

            return json_encode([
                'response' => 'ok',
            ]);

        }catch (\Exception $exception){
            return json_encode([
                'response' => 'error'
            ]);
        }

    }


    public function configureActions()
    {
        return [
            'ajaxHandler' => [ // Ajax-метод
                'prefilters' => [
                    new ActionFilter\Authentication()
                ],
            ],
        ];
    }


    /**
     * @return array
     * Получение всех элементов инфоблока.
     * В контексте этого компонента это пользователи
     */
    public function getUsers()
    {
        $iblock = CIBlockElement::GetList([], [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID']
        ],
            false,
            false,
            ['ID', 'NAME']);

        $users = [];
        while ($ob = $iblock->GetNextElement()) {
            $fields = $ob->GetFields();
            $users[] = [
                'ID' => $fields['ID'],
                'NAME' => $fields['NAME'],
            ];
        }

        return $users;
    }


    /**
     * @param $all_slots - все сгенерированные слоты
     * (ещё не назначенные и не проверенные с БД) [day1 => [slot1, slot2], day2 => [slot1, slot2]]
     *
     * @param $db_slots - слоты имеющиеся в БД
     * @return array
     * Сопоставляет сгенерированные слоты, слоты в БД и формирует масив для отображения в шаблоне
     */
    public function getGeneratedSlots($all_slots, $db_slots)
    {
        $view_slots = [];
        // Проходим по сгенерированным слотам
        foreach ($all_slots as $day => $day_values) {
            // заходим в каждый день со слотами
            foreach ($day_values as $slot) {
                // проходим по массиву из БД
                // и сверяем каждый слот с элементом массива из БД
                foreach ($db_slots as $db_slot) {
                    $db_slot['FREE'] = 'Y';
                    // если сгенерированный слот есть в слотах из БД помечаем его. пригодится в шаблоне
                    // формируем масив слота для шаблона со служебной информацией
                    if (is_array($db_slot['VALUE']) && in_array($slot, $db_slot['VALUE'])){
                        $item = [
                            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                            'ELEMENT_ID' => $db_slot['ELEMENT_ID'],
                            'PROPERTY_ID' => $db_slot['PROPERTY_ID'],
                            'NAME' => $db_slot['NAME'],
                            'SLOT' => ['VALUE' => $slot, 'FREE' => 'N'],
                        ];
                        break;
                    }else{
                        $item = [
                            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                            'ELEMENT_ID' => '',
                            'PROPERTY_ID' => '',
                            'NAME' => '',
                            'SLOT' => ['VALUE' => $slot, 'FREE' => 'Y'],
                        ];
                    }
                }
                $view_slots[$day][] = $item;
            }
        }

        return $view_slots;
    }


    /**
     * @return array
     * @throws \Bitrix\Main\ObjectException
     * Получение всех элементов инфоблока.
     * В контексте этого компонента это все возможные слоты в БД
     */
    public function getDBSlots()
    {
        $db_slots = [];

        $res = CIBlockElement::GetList([], [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID']
        ],
            false,
            false,
        );

        while ($ob = $res->GetNextElement()) {
            $fields = $ob->GetFields();
            $db_slots[] = [
                'NAME' => $fields['NAME'],
                'ELEMENT_ID' => $fields['ID'],
            ];
        }

        $filter = ['IBLOCK_ID' => $this->arParams['IBLOCK_ID']];
        $props = [];
        CIBlockElement::GetPropertyValuesArray($props, $this->arParams['IBLOCK_ID'], $filter);

        foreach ($props as $element_id => $prop) {
            foreach ($db_slots as $key => $item) {
                if ($item['ELEMENT_ID'] == $element_id) {
                    if (is_array($prop[$this->arParams['SLOT_DATETIME']]['VALUE'])){
                        $db_slots[$key]['VALUE'] = $prop[$this->arParams['SLOT_DATETIME']]['VALUE'];
                        $db_slots[$key]['PROPERTY_ID'] = $prop[$this->arParams['SLOT_DATETIME']]['ID'];
                        break;
                    }
                }
            }
        }

        return $db_slots;
    }


    /**
     * @return array
     * @throws \Bitrix\Main\ObjectException
     * Конвертирует слоты из масива DateTime в обычный масив
     */
    public function getTotalSlotsAsArray()
    {
        $all_slots = [];
        foreach ($this->getTotalSlots() as $day => $value) {
            /** @var DateTime $slot */
            foreach ($value as $slot) {
                $all_slots[$day][] = trim($slot->format(self::FORMATE_DATE_TIME));
            }
        }

        return $all_slots;
    }


    /**
     * @return array
     * @throws \Bitrix\Main\ObjectException
     * Генерирует слоты на количество дней указанное в параметрах
     */
    public function getTotalSlots()
    {
        $all_slots = [];
        for ($i = 0; $i < $this->arParams['END_DAY'] * 1; $i++) {
            if ($i > 0) {
                $all_slots[(new DateTime())->add("$i days")->format('d.m.Y')] = $this->getTimeSlots((new DateTime())->add("$i days"));
            } else {
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
}
