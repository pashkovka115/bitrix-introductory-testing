<?php

namespace YLab\Components;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\UI\PageNavigation;
use \CBitrixComponent;
use \Exception;
use Bitrix\Main\UI\Filter\Options;

/**
 * Компонент отображения и поиска компаний/должностей
 *
 * Class PositionsListComponent
 * @package YLab\Components
 *
 */
class PositionsListComponent extends CBitrixComponent
{

    /** @var string $templateName Имя шаблона компонента */
    private $templateName;

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
        $this->templateName = $this->GetTemplateName();

        if (!empty($this->arParams['POSITIONS_HL_NAME'])) {
            $this->positionsHlblockName = $this->arParams['POSITIONS_HL_NAME'];
        }

        if (!empty($this->arParams['ORGANIZATIONS_HL_NAME'])) {
            $this->organizationsHlblockName = $this->arParams['ORGANIZATIONS_HL_NAME'];
        }

        if ($this->arResult['IS_HL_MODULE_INCLUDED'] &&
          !empty($this->arParams['POSITIONS_HL_NAME']) &&
          !empty($this->arParams['ORGANIZATIONS_HL_NAME'])) {

            if ($this->templateName == 'grid') {
                $this->showByGrid();
            } else {
                $this->arResult['ITEMS']['GRID_NAME'] = $this->getGridId();
                $this->arResult['ITEMS']['GRID_HEAD'] = $this->getGridHead();
                $this->arResult['ITEMS']['ELEMENTS'] = $this->getElements();
            }
        }


        $this->includeComponentTemplate();
    }


    /**
     * Получение элементов HL блоков
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getElements(): array
    {
        $result = [];

        $positionsHlblockId = $this->getHlBlockIdByName($this->positionsHlblockName);
        $organizationsHlblockId = $this->getHlBlockIdByName($this->organizationsHlblockName);

        $positionsDataClass = $this->getEntityDataClass($positionsHlblockId);
        $organizationsProgramDataClass = $this->getEntityDataClass($organizationsHlblockId);

        if (!$this->getGridNav()->allRecordsShown()) {
            $arNav['iNumPage'] = $this->getGridNav()->getCurrentPage();
            $arNav['nPageSize'] = $this->getGridNav()->getPageSize();
        } else {
            $arNav = false;
        }

        $arFilter = $this->getGridFilterValues();

        $arCurSort = $this->getObGridParams()->getSorting(['sort' => ['ID' => 'DESC']])['sort'];

        $elements = $positionsDataClass::getList([
          'filter' => $arFilter,
          "count_total" => true,
          'select' => array(
            "ID",
            "UF_POSITION_NAME",
            "ORGANIZATION_NAME" => "ORGANIZATION.UF_COMPANY_NAME",
            "ORGANIZATION_ADDRESS" => "ORGANIZATION.UF_COMPANY_ADDRESS",
            "ORGANIZATION_INN_CODE" => "ORGANIZATION.UF_COMPANY_INN_CODE",
          ),
          'order' => $arCurSort,
          "offset" => $this->getGridNav()->getOffset(),
          "limit" => $this->getGridNav()->getLimit(),
          'runtime' => array(
            'ORGANIZATION' => array(
              'data_type' => $organizationsProgramDataClass,
              'reference' => array(
                '=this.UF_COMPANY_ID' => 'ref.ID'
              ),
              'join_type' => 'inner'
            ),
          ),
        ]);

        $this->getGridNav()->setRecordCount($elements->getCount());

        $result = $elements->fetchAll();

        return $result;
    }


    /**
     * Отображение через грид
     */
    public function showByGrid()
    {
        $this->arResult['GRID_ID'] = $this->getGridId();

        $this->arResult['GRID_BODY'] = $this->getGridBody();
        $this->arResult['GRID_HEAD'] = $this->getGridHead();

        $this->arResult['GRID_NAV'] = $this->getGridNav();
        $this->arResult['GRID_FILTER'] = $this->getGridFilterParams();
        $this->arResult['RECORD_COUNT'] = $this->getGridNav()->getRecordCount();

    }

    /**
     * Возвращает содержимое (тело) таблицы.
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getGridBody(): array
    {
        $arBody = [];

        $arItems = $this->getElements();

        foreach ($arItems as $arItem) {
            $arGridElement = [];

            $arGridElement['data'] = [
              'ID' => $arItem['ID'],
              'UF_POSITION_NAME' => $arItem['UF_POSITION_NAME'],
              'ORGANIZATION_NAME' => $arItem['ORGANIZATION_NAME'],
              'ORGANIZATION_ADDRESS' => $arItem['ORGANIZATION_ADDRESS'],
              'ORGANIZATION_INN_CODE' => $arItem['ORGANIZATION_INN_CODE'],
            ];

            $arBody[] = $arGridElement;
        }

        return $arBody;
    }


    /**
     * Возвращает идентификатор грида.
     *
     * @return string
     */
    private function getGridId(): string
    {
        return 'ylab_position_list_' . $this->positionsHlblockName;
    }


    /**
     * Возращает заголовки таблицы.
     *
     * @return array
     */
    private function getGridHead(): array
    {
        return [
          [
            'id' => 'ID',
            'name' => 'ID',
            'default' => true,
            'sort' => 'ID',
          ],
          [
            'id' => 'UF_POSITION_NAME',
            'name' => Loc::getMessage('YLAB_POSITIONS_LIST_POSITION_NAME'),
            'default' => true,
            'sort' => 'UF_POSITION_NAME',
          ],
          [
            'id' => 'ORGANIZATION_NAME',
            'name' => Loc::getMessage('YLAB_POSITIONS_LIST_ORGANIZATION_NAME'),
            'default' => true,
            'sort' => 'ORGANIZATION_NAME',
          ],
          [
            'id' => 'ORGANIZATION_ADDRESS',
            'name' => Loc::getMessage('YLAB_POSITIONS_LIST_ORGANIZATION_ADDRESS'),
            'default' => true,
            'sort' => 'ORGANIZATION_ADDRESS',
          ],
          [
            'id' => 'ORGANIZATION_INN_CODE',
            'name' => Loc::getMessage('YLAB_POSITIONS_LIST_ORGANIZATION_INN_CODE'),
            'default' => true,
            'sort' => 'ORGANIZATION_INN_CODE',
          ],

        ];
    }

    /**
     * Возвращает настройки отображения грид фильтра.
     *
     * @return array
     */
    private function getGridFilterParams(): array
    {
        return [
          [
            'id' => 'ID',
            'name' => 'ID',
            'type' => 'number'
          ],
          [
            'id' => 'UF_POSITION_NAME',
            'name' => Loc::getMessage('YLAB_POSITIONS_LIST_POSITION_NAME'),
            'type' => 'string'
          ],
          [
            'id' => 'ORGANIZATION_NAME',
            'name' => Loc::getMessage('YLAB_POSITIONS_LIST_ORGANIZATION_NAME'),
            'type' => 'string'
          ],
          [
            'id' => 'ORGANIZATION_ADDRESS',
            'name' => Loc::getMessage('YLAB_POSITIONS_LIST_ORGANIZATION_ADDRESS'),
            'type' => 'string'
          ],
          [
            'id' => 'ORGANIZATION_INN_CODE',
            'name' => Loc::getMessage('YLAB_POSITIONS_LIST_ORGANIZATION_INN_CODE'),
            'type' => 'string'
          ],

        ];
    }

    /**
     * Возвращает единственный экземпляр настроек грида.
     *
     * @return GridOptions
     */
    private function getObGridParams(): GridOptions
    {
        return $this->gridOption ?? $this->gridOption = new GridOptions($this->getGridId());
    }

    /**
     * Параметры навигации грида
     *
     * @return PageNavigation
     */
    private function getGridNav(): PageNavigation
    {
        if ($this->gridNav === null) {
            $this->gridNav = new PageNavigation($this->getGridId());
            $this->gridNav->allowAllRecords(true)->setPageSize($this->getObGridParams()->GetNavParams()['nPageSize'])
              ->initFromUri();
        }

        return $this->gridNav;
    }

    /**
     * Возвращает значения грид фильтра.
     *
     * @return array
     */
    public function getGridFilterValues(): array
    {
        $obFilterOption = new Options($this->getGridId());
        $arFilterData = $obFilterOption->getFilter([]);
        $baseFilter = array_intersect_key($arFilterData, array_flip($obFilterOption->getUsedFields()));
        $formatedFilter = $this->prepareFilter($arFilterData, $baseFilter);

        return array_merge(
          $baseFilter,
          $formatedFilter
        );
    }

    /**
     * Подготавливает параметры фильтра
     * @param array $arFilterData
     * @param array $baseFilter
     * @return array
     */
    public function prepareFilter(array $arFilterData, &$baseFilter = []): array
    {

        $arFilter = [];

        if (!empty($arFilterData['ID_from'])) {
            $arFilter['>=ID'] = (int)$arFilterData['ID_from'];
        }
        if (!empty($arFilterData['ID_to'])) {
            $arFilter['<=ID'] = (int)$arFilterData['ID_to'];
        }

        return $arFilter;
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
     * Получение entityDataClass HL блока
     *
     * @param $hlblockId
     * @return \Bitrix\Main\ORM\Data\DataManager|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getEntityDataClass($hlblockId)
    {

        if (empty($hlblockId) || $hlblockId < 1) {
            return false;
        }

        $entityDataClass = null;

        if (Loader::IncludeModule('highloadblock')) {

            try {
                $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entityDataClass = $entity->getDataClass();
            } catch (\Bitrix\Main\SystemException $e) {
                echo 'Error - The HL block does not exist';
            }
        }

        return $entityDataClass;
    }

}
