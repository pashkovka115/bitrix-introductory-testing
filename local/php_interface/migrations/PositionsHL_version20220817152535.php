<?php

namespace Sprint\Migration;


class PositionsHL_version20220817152535 extends Version
{
    protected $description = "Миграция для HL Positions";

    protected $moduleVersion = "4.1.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock(array (
  'NAME' => 'Positions',
  'TABLE_NAME' => 'y_hl_positions',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'Должности',
    ),
    'en' => 
    array (
      'NAME' => 'Positions',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_POSITION_NAME',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => 'POSITION_NAME',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'Y',
  'SHOW_FILTER' => 'S',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 3,
    'MAX_LENGTH' => 100,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Position name',
    'ru' => 'Наименование',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Position name',
    'ru' => 'Наименование',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Position name',
    'ru' => 'Наименование',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_POSITION_CODE',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => 'POSITION_CODE',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'Y',
  'SHOW_FILTER' => 'S',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 3,
    'MAX_LENGTH' => 20,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Position code',
    'ru' => 'Кол должности',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Position code',
    'ru' => 'Кол должности',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Position code',
    'ru' => 'Кол должности',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_COMPANY_NAME',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => 'COMPANY_NAME',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'Y',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 3,
    'MAX_LENGTH' => 250,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'COMPANY NAME',
    'ru' => 'Название компании',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'COMPANY NAME',
    'ru' => 'Название компании',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'COMPANY NAME',
    'ru' => 'Название компании',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
));
            $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_COMPANY_ID',
  'USER_TYPE_ID' => 'integer',
  'XML_ID' => 'COMPANY_ID',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'N',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'MIN_VALUE' => 0,
    'MAX_VALUE' => 0,
    'DEFAULT_VALUE' => NULL,
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'COMPANY ID',
    'ru' => 'ID Компании',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'COMPANY ID',
    'ru' => 'ID Компании',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'COMPANY ID',
    'ru' => 'ID Компании',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => '',
    'ru' => '',
  ),
));
        }

    public function down()
    {
        //your code ...
    }
}
