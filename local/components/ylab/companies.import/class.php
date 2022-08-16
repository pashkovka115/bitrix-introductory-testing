<?php


use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Loader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Engine\ActionFilter;

require_once 'ChunkReadFilter2.php';

/**
 * Компонент импорта Организаций
 *
 * Class OrganizationsImportComponent
 * @package YLab\Components
 *
 */
class OrganizationsImportComponent extends CBitrixComponent implements Controllerable
{

    /**
     * Метод executeComponent()
     *
     * @return mixed|void|null
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        // Проверка на подключение модуля highloadblock
        if (Loader::IncludeModule('highloadblock')) {
            $this->arResult['IS_HL_MODULE_INCLUDED'] = true;
            $this->arResult['COMPONENT_ID'] = $this->componentId();
            $this->arResult['SCRIPT_PATH'] = $_SERVER['SCRIPT_NAME'];
            $this->arResult['COMPONENT_DIRECTORY'] = $this->GetPath();
        } else {
            $this->arResult['IS_HL_MODULE_INCLUDED'] = false;
        }

        if (!empty($this->arParams['ORGANIZATIONS_HL_NAME'])) {
            $this->arResult['ORGANIZATIONS_HL_NAME'] = $this->arParams['ORGANIZATIONS_HL_NAME'];
        }


        $this->includeComponentTemplate();
    }


    /**
     * Медод установки фильтров
     * Сбрасываем фильтры по-умолчанию (ActionFilter\Authentication и ActionFilter\HttpMethod)
     * Обязательный метод
     *
     * @return array[][]
     */
    public function configureActions()
    {
        return [
          'organizationImport' => [
            'prefilters' => []
              , 'postfilters' => []
          ]
        ];
    }


    /**
     * Метод Action обработка скрипта ajax после нажатия на кнопку Импорт
     *
     * @param $post
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function organizationImportAction($post)
    {

        $result = [];

        $path = $_SERVER['DOCUMENT_ROOT'] . (CFile::getPath($post['id']));

        $pathPieces = explode("/", $path);
        $file = array_pop($pathPieces);
        $pieces = explode(".", $file);
        $orgFileName = (string)$pieces[0];
        $orgFileExtension = (string)$pieces[1];

        if (strcmp(trim(mb_strtolower($orgFileName)), "organizations") != 0) {
            return $result = "not_right_file";
        }
        if ((strcmp(trim(mb_strtolower($orgFileExtension)), "xlsx") != 0) &&
          (strcmp(trim(mb_strtolower($orgFileExtension)), "xls") != 0))
        {
            return $result = "not_right_file";
        }

        $inputFileType = 'Xlsx';

        if ($orgFileExtension == "xls") {
            $inputFileType = 'Xls';
        }

        /**  чтение чанками  **/
        $session = Bitrix\Main\Application::getInstance()->getSession();

        if ($session->has('startRow')) {
            $startRow = $session->get('startRow');
        } else {
            $startRow = 2;
        }
        // размер чанка
        $chunkSize = 5;

        if ($inputFileType == "Xlsx") {
            $reader = IOFactory::createReader('Xlsx');
        }
        if ($inputFileType == "Xls") {
            $reader = IOFactory::createReader('Xls');
        }

        $chunkFilter = new ChunkReadFilter2();

        // Основной цикл чтения из xlsx/xls чанками по 5 и последующей записи
        do {
            $chunkFilter->setRows($startRow, $chunkSize);
            $reader->setReadFilter($chunkFilter);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);

            // координаты ячеек в чанке
            $coordinates = $spreadsheet->getActiveSheet()->getCoordinates();

            // Массив со значениями ячеек
            $cellValues = [];
            foreach ($coordinates as $coordinate) {
                $cellValues[] = $spreadsheet->getActiveSheet()->getCell($coordinate)->getValue();
            }

            // Разбиваем массив чанками по 3, что соответствует столбцам A,B,C таблицы exel
            $cellValuesAdapted = array_chunk($cellValues, 3);


            /**  запись в HL блока Организации  **/

            $hlblockId = $this->getHlBlockIdByName('Organizations');

            // Подготавливаем параметры для выборки из HL блока
            $fetchHlParams = array('select' => ['*']);

            $elements = $this->fetchAll($hlblockId, $fetchHlParams);

            // массив с существующими ИНН
            $arrInnCodes = [];
            foreach ($elements as $element) {
                $arrInnCodes[] = $element["UF_COMPANY_INN_CODE"];
            }

            // пишем записи в HL
            foreach ($cellValuesAdapted as $cellValuesChunk) {


                if (!in_array(trim((string)$cellValuesChunk[2]), $arrInnCodes) && !empty((string)$cellValuesChunk[2])) {

                    $record = [];
                    $record += ['UF_COMPANY_NAME' => trim($cellValuesChunk[0])];
                    $record += ['UF_COMPANY_ADDRESS' => trim($cellValuesChunk[1])];
                    $record += ['UF_COMPANY_INN_CODE' => trim((string)$cellValuesChunk[2])];

                    $this->addHLblockRecords($hlblockId, $record);
                }

            }

            $startRow += $chunkSize;
            $session->set('startRow', $startRow);

            unset($reader);
            unset($spreadsheet);

        } while ($coordinates);


        $result = "the_end";

        $session->remove('startRow');

        return $result;
    }


    /**
     * Возвращает ID компонента
     *
     * @return mixed
     */
    protected function componentId()
    {
        $entryId = 'organizations';
        $m = null;
        /* вычленим только уникальную цифровую часть идентификатора */
        if (preg_match('/^bx_(.*)_' . $entryId . '$/',
          $this->getEditAreaId($entryId),)) {
            return $m[1];
        }
    }


    /**
     * Метод возвращает ID HL блока по названию сущности
     *
     * @param $name
     * @return false|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHlBlockIdByName($name)
    {
        if (!Loader::IncludeModule('highloadblock')) {
            return false;
        }

        $HL = HL\HighloadBlockTable::getList([
          'select' => ['ID'],
          'filter' => ['NAME' => $name],
          'limit' => '1',
          'cache' => ['ttl' => 3600],
        ]);
        $return = $HL->fetch();

        if (!$return) {
            return false;
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

    public static function isHlexist($hlblockId): bool
    {
        $syteHlblockIds = [];
        $isHlexist = false;

        if (Loader::IncludeModule('highloadblock')) {
            $hlblocksIds = HL\HighloadBlockTable::getList([
              'select' => array('ID'),
              'cache' => [
                'ttl' => 3600
              ]
            ])->fetchAll();
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
     * Выборка данных из HL по параметрам выборки
     *
     * @param $entityDataClass
     * @param $filter
     * @param $select
     * @param $order
     * @param $offset
     * @param $limit
     * @return mixed
     */

    private function fetchAll($hlblockId, $fetchHlParams)
    {

        $entityDataClass = $this->getEntityDataClass($hlblockId);

        $defaultParams = array(
          'filter' => array(),
          "count_total" => true,
          'select' => array(),
          'order' => array(),
          "offset" => array(),
          "limit" => array(),
          'cache' => array(
            'ttl' => 3600,
            'cache_joins' => false,
          )
        );

        $params = array_merge($defaultParams, $fetchHlParams);

        return $entityDataClass::GetList($params)->fetchAll();
    }


    /**
     * Возвращает EntityDataClass HL блока
     *
     * @param $hlblockId
     * @return \Bitrix\Main\ORM\Data\DataManager|string|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */

    private function getEntityDataClass($hlblockId)
    {
        $entityDataClass = null;

        if (Loader::IncludeModule('highloadblock')) {
            $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entityDataClass = $entity->getDataClass();
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

    public function addHLblockRecords($hlblockId, $record)
    {
        if (Loader::IncludeModule('highloadblock') && $this->isHlexist($hlblockId)) {
            $entityDataClass = $this->getEntityDataClass($hlblockId);
            $entityDataClass::add($record);
        }
    }

}