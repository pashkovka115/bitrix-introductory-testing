<?php

namespace YLab\Components;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \Exception;
use \CBitrixComponent;


/**
 * Компонент для импорта компаний/должностей
 *
 * Class PositionsListComponent
 * @package YLab\Components
 *
 */
class PositionsImportComponent extends CBitrixComponent
{

    /** @var string $positionsHlblockName Символьный код hl блока Должности */
    private string $positionsHlblockName;

    /** @var string $organizationsHlblockName Символьный код hl блока Организации */
    private string $organizationsHlblockName;


    /**
     * Метод executeComponent
     *
     * @return mixed|void|null
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {

        // Проверка на подключение модуля highloadblock
        if (Loader::IncludeModule('highloadblock')) {
            $this->arResult['IS_HL_MODULE_INCLUDED'] = true;
        } else {
            $this->arResult['IS_HL_MODULE_INCLUDED'] = false;
        }

        // Инициализация параметров компонента
        if (!empty($this->arParams['POSITIONS_HL_NAME'])) {
            $this->positionsHlblockName = $this->arParams['POSITIONS_HL_NAME'];
        }

        if (!empty($this->arParams['ORGANIZATIONS_HL_NAME'])) {
            $this->organizations_hlblock_name = $this->arParams['ORGANIZATIONS_HL_NAME'];
        }

        // Очистка параметров POST
        if (!isset($_SESSION)) {
            session_start();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_SESSION['postdata'] = $_POST;
            unset($_POST);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
        if (array_key_exists('postdata', $_SESSION)) {
            unset($_SESSION['postdata']);
        }

        // Работа логики компонента
        if ($this->arResult['IS_HL_MODULE_INCLUDED'] &&
          !empty($this->arParams['POSITIONS_HL_NAME']) &&
          !empty($this->arParams['ORGANIZATIONS_HL_NAME'])) {

            $request = Application::getInstance()->getContext()->getRequest();

            $action = $request->get('action');

            if ($action == "import_xlsx") {
                $this->arResult['IS_RIGHT_FILE_EXTENSION'] = true;
                $this->importOrganizationsDataFromExel();
                $this->importPositionsDataFromExel();
            }
        }


        $this->includeComponentTemplate();
    }


    /**
     * Метод импорта данных из файла xlsx данных об Организациях
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function importOrganizationsDataFromExel()
    {

        $path = $_FILES['file-upload']['tmp_name'];
        $name = $_FILES['file-upload']['name'];

        $file_extension = explode(".", $name)[1];

        if ($file_extension != "xlsx") {
            $this->arResult['IS_RIGHT_FILE_EXTENSION'] = false;
            return;
        }

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($path);
        // Только чтение данных
        $reader->setReadDataOnly(true);

        $allSheetsNames = $spreadsheet->getSheetNames();

        $organizationsData = null;

        if (in_array($this->organizationsHlblockName, $allSheetsNames)) {
            $organizationsData = $spreadsheet->getSheetByName($this->organizationsHlblockName)->toArray();
        }

        // Массив с заголовками таблицы в xlsx (заголовки должны быть в 1-й строке документа/таблицы)
        $orgFieldCodes = [];
        // Массив с данными из xlsx
        $orgRecords = [];

        if (!is_null($organizationsData)) {
            $orgRecords = &$organizationsData;
            $orgFieldCodes = array_shift($orgRecords);
        }

        $hlblockId = $this->getHlBlockIdByName($this->organizationsHlblockName);
        $hlBlockFieldsNames = [];

        if ($this->isHlexist($hlblockId)) {
            $hlBlockFieldsNames = $this->getHlBlockFieldsNames($hlblockId);
        }

        $fetchAll = $this->fetchAll($this->getEntityDataClass($hlblockId), array(), array('*'), array(), array(), array());


        // Если все поля из заголовка таблицы страницы xlsx есть в выбранном HL то пишем
        if (empty(array_diff($orgFieldCodes, array_intersect($orgFieldCodes, $hlBlockFieldsNames)))) {

            foreach ($orgRecords as $orgRecord) {
                $rs = [];
                $writePermission = true;
                foreach ($orgRecord as $key => $value) {

                    foreach ($orgFieldCodes as $k => $v) {
                        if ($key == $k) {
                            $rs[$v] = $value;
                        }
                    }

                }
                // Если запись из xlsx уже есть в HL то не пишем
                foreach ($fetchAll as $element) {
                    if ($rs['UF_COMPANY_INN_CODE'] == $element['UF_COMPANY_INN_CODE']) {
                        $writePermission = false;
                    }
                }
                if ($writePermission) {
                    $this->addHLblockRecords($this->getEntityDataClass($hlblockId), $rs);
                }

            }

        }

    }


    /**
     *  Метод импорта данных из файла xlsx данных об Должностях
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function importPositionsDataFromExel()
    {

        $path = $_FILES['file-upload']['tmp_name'];
        $name = $_FILES['file-upload']['name'];

        $file_extension = explode(".", $name)[1];

        if ($file_extension != "xlsx") {
            $this->arResult['IS_RIGHT_FILE_EXTENSION'] = false;
            return;
        }

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($path);
        // Только чтение данных
        $reader->setReadDataOnly(true);

        $allSheetsNames = $spreadsheet->getSheetNames();

        $positionsData = null;

        if (in_array($this->positionsHlblockName, $allSheetsNames)) {
            $positionsData = $spreadsheet->getSheetByName($this->positionsHlblockName)->toArray();
        }

        // Массив с заголовками таблицы в xlsx (заголовки должны быть в 1-й строке документа/таблицы)
        $positionsFieldCodes = [];
        // Массив с данными из xlsx
        $positionsRecords = [];

        if (!is_null($positionsData)) {
            $positionsRecords = &$positionsData;
            $positionsFieldCodes = array_shift($positionsRecords);
        }


        $hlblockId = $this->getHlBlockIdByName($this->positionsHlblockName);
        $hlBlockFieldsNames = [];

        if ($this->isHlexist($hlblockId)) {
            $hlBlockFieldsNames = $this->getHlBlockFieldsNames($hlblockId);
        }


        $fetchAll = $this->fetchAll($this->getEntityDataClass($hlblockId), array(), array('*'), array(), array(), array());


        // Если все поля из заголовка таблицы страницы xlsx есть в выбранном HL то пишем
        if (empty(array_diff($positionsFieldCodes, array_intersect($positionsFieldCodes, $hlBlockFieldsNames)))) {

            foreach ($positionsRecords as $positionRecord) {
                $rs = [];
                $writePermission = true;
                foreach ($positionRecord as $key => $value) {

                    foreach ($positionsFieldCodes as $k => $v) {
                        if ($key == $k) {
                            $rs[$v] = $value;
                        }
                    }

                }
                // Если запись из xlsx уже есть в HL то не пишем
                foreach ($fetchAll as $element) {
                    if ($rs['UF_COMPANY_CODE'] == $element['UF_COMPANY_CODE'] &&
                      $rs['UF_POSITION_CODE'] == $element['UF_POSITION_CODE']) {
                        $writePermission = false;
                    }
                }
                if ($writePermission) {
                    $this->addHLblockRecords($this->getEntityDataClass($hlblockId), $rs);
                }

            }

        }

    }


    /**
     *  Метод возвращает ID HL блока по названию сущности
     *
     * @param $name
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHlBlockIdByName($name)
    {
        $return = null;

        if (Loader::IncludeModule('highloadblock')) {
            $HL = HL\HighloadBlockTable::getList([
              'select' => ['ID'],
              'filter' => ['NAME' => $name],
              'limit' => '1',
              'cache' => ['ttl' => 3600],
            ]);
            $return = $HL->fetch();
        }

        if (!$return) {
            throw new Exception('HL block with name "' . $name . '" not found');
        }

        return $return['ID'];
    }


    /**
     * Метод проверяет существует ли HL в системе
     *
     * @param $hlblockId
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function isHlexist($hlblockId) : bool
    {
        $syteHlblockIds = [];
        $isHlexist = false;

        if (Loader::IncludeModule('highloadblock')) {
            $hlblocksIds = HL\HighloadBlockTable::getList(['select' => array('ID')])->fetchAll();
            foreach ($hlblocksIds as $hlblocksId) {
                $syteHlblockIds[] = $hlblocksId['ID'];
            }
        }

        if (in_array($hlblockId, $syteHlblockIds)) {
            $isHlexist = true;
        }
        return $isHlexist;
    }


    /**
     * Получение массива имен полей HL блока
     *
     * @param $hlblockId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHlBlockFieldsNames($hlblockId)
    {
        $hlBlockFieldsNames = [];

        if (Loader::IncludeModule('highloadblock')) {
            $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $hlBlockFields = $entity->getFields();
            $hlBlockFieldsNames = [];
            foreach ($hlBlockFields as $key => $value) {
                $hlBlockFieldsNames[] = $key;
            }
        }

        return $hlBlockFieldsNames;
    }


    /**
     * Выборка данных из HL по заданным параметрам
     *
     * @param $entityDataClass
     * @param $filter
     * @param $select
     * @param $order
     * @param $offset
     * @param $limit
     * @return mixed
     */
    private function fetchAll($entityDataClass, $filter, $select, $order, $offset, $limit)
    {
        return $entityDataClass::GetList([
          'filter' => $filter,
          "count_total" => true,
          'select' => $select,
          'order' => $order,
          "offset" => $offset,
          "limit" => $limit,
          'cache' => array(
            'ttl' => 3600,
            'cache_joins' => false,
          )
        ])->fetchAll();
    }


    /**
     * Возвращает EntityDataClass HL блока
     *
     * @param $hlblockId
     * @return \Bitrix\Main\ORM\Data\DataManager|string|null
     * @throws \Bitrix\Main\LoaderException
     */
    private function getEntityDataClass($hlblockId)
    {
        $entityDataClass = null;

        if (Loader::IncludeModule('highloadblock')) {

            try {
                $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entityDataClass = $entity->getDataClass();
            } catch (\Bitrix\Main\SystemException $e) {
                echo 'Error - The HL block does not exist';
                echo '<br>';
            }
        }

        return $entityDataClass;
    }


    /**
     * Добавление записи в HL блок
     *
     * @param $entityDataClass
     * @param $record
     * @throws \Bitrix\Main\LoaderException
     */
    private function addHLblockRecords($entityDataClass, $record)
    {
        if (!is_null($entityDataClass) && Loader::IncludeModule('highloadblock')) {
            $entityDataClass::add($record);
        }
    }

}