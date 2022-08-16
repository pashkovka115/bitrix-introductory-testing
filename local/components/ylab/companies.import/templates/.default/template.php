<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die;

use Bitrix\Main\Localization\Loc;

CUtil::InitJSCore();
CJSCore::Init(array("fx", "ajax"));
?>

<? if (!$arResult['IS_HL_MODULE_INCLUDED']) : ?>

    <?= Loc::getMessage('YLAB_COMPANIES_IMPORT_TEMPLATE_ERROR1') ?>
    <?= '<br>' ?>

<? else: ?>

    <? if (empty($arParams['ORGANIZATIONS_HL_NAME'])) : ?>
        <?= Loc::getMessage('YLAB_COMPANIES_IMPORT_TEMPLATE_ERROR2') ?>
        <?= '<br>' ?>
    <? endif; ?>


    <? if (!empty($arResult['ORGANIZATIONS_HL_NAME'])) : ?>
        <h3 class=""><?= Loc::getMessage('YLAB_COMPANIES_IMPORT_FORM_TITLE') ?></h3>
        <div id="progress-bar2"><?= Loc::getMessage('YLAB_COMPANIES_IMPORT_FORM_PROGRESS_BAR_CAPTION') ?></div>
        <div id="content2"></div>
        <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" id="import-form2">

            <? $APPLICATION->IncludeComponent("bitrix:main.file.input", "",
              array(
                "INPUT_NAME" => "NEW_FILE_UPLOAD2",
                "MULTIPLE" => "N",
                "MODULE_ID" => "main",
                "MAX_FILE_SIZE" => "",
                "ALLOW_UPLOAD" => "F",
                "ALLOW_UPLOAD_EXT" => 'xlsx, xls',
                "INPUT_VALUE" => $_POST['NEW_FILE_UPLOAD2']
              ),
              false

            ); ?>

            <input type="submit" id="import-button2"
                   value=<?= Loc::getMessage('YLAB_COMPANIES_IMPORT_FORM_BUTTON_TITLE') ?>>
        </form>

    <? endif; ?>
<? endif; ?>

<?php
$file_id = $_POST['NEW_FILE_UPLOAD2'];
?>


<script>

    button2 = BX('import-button2');
    content2 = BX('content2');
    progress_bar2 = BX('progress-bar2');


    BX.bind(button2, 'click', () => {
        repeat_import2();
    });


    function repeat_import2() {
        BX.ajax.runComponentAction('ylab:companies.import',
            'organizationImport', {
                mode: 'class',
                data: {post: {"id": <?=$file_id?>}},
                method: 'POST',
                dataType: 'json',
                timeout: 50000,
            })
            .then(function (response) {
                if (response.data === 'the_end') {
                    // Если форма успешно отправилась
                    console.log(response);
                    content2.innerText = <?='"' . Loc::getMessage('YLAB_COMPANIES_IMPORT_FORM_FINISHED') . '"'?>;

                } else if (response.data === 'not_right_file') {
                    console.log(response);
                    content2.innerText = <?='"' . Loc::getMessage('YLAB_COMPANIES_IMPORT_FORM_WRONG_EXTENSION') . '"'?>;
                } else {
                    content2.innerText = response;
                    repeat_import2();
                }
            }, function (response) {
                //сюда будут приходить все ответы, у которых status !== 'success'
                console.log(response);
                progress_bar2.append("I");
                repeat_import2();
            });
    }
</script>