<?php

namespace Sprint\Migration;


class Version20220815151847 extends Version
{
    protected $description = "Миграция для HL Organizations";

    protected $moduleVersion = "4.1.1";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock(array (
  'NAME' => 'Organizations',
  'TABLE_NAME' => 'y_hl_organizations',
  'LANG' => 
  array (
    'ru' => 
    array (
      'NAME' => 'Организации',
    ),
    'en' => 
    array (
      'NAME' => 'Organizations',
    ),
  ),
));
        $helper->Hlblock()->saveField($hlblockId, array (
  'FIELD_NAME' => 'UF_COMPANY_NAME',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => 'COMPANY_NAME',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'S',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 100,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 1,
    'MAX_LENGTH' => 255,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Company name',
    'ru' => 'Название компании',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Company name',
    'ru' => 'Название компании',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Company name',
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
  'FIELD_NAME' => 'UF_COMPANY_INN_CODE',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => 'COMPANY_INN_CODE',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'Y',
  'SHOW_FILTER' => 'S',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 12,
    'ROWS' => 1,
    'REGEXP' => '/\\d{12}/ ',
    'MIN_LENGTH' => 12,
    'MAX_LENGTH' => 12,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Company INN code',
    'ru' => 'ИНН компании',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Company INN code',
    'ru' => 'ИНН компании',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Company INN code',
    'ru' => 'ИНН компании',
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
  'FIELD_NAME' => 'UF_COMPANY_ADDRESS',
  'USER_TYPE_ID' => 'string',
  'XML_ID' => 'COMPANY_ADDRESS',
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'S',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'SIZE' => 20,
    'ROWS' => 1,
    'REGEXP' => '',
    'MIN_LENGTH' => 1,
    'MAX_LENGTH' => 225,
    'DEFAULT_VALUE' => '',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Company address',
    'ru' => 'Адрес компании',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Company address',
    'ru' => 'Адрес компании',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Company address',
    'ru' => 'Адрес компании',
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
