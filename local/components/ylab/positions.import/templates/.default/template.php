<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die;

use Bitrix\Main\Localization\Loc;

CUtil::InitJSCore();
CJSCore::Init(array("fx", "ajax"));
?>


<? if (!empty($arParams['POSITIONS_HL_NAME']) && !empty($arParams['ORGANIZATIONS_HL_NAME'])) : ?>
    <h3 class=""><?= Loc::getMessage('YLAB_POSITIONS_IMPORT_FORM_TITLE') ?></h3>
    <div id="progress-bar"><?= Loc::getMessage('YLAB_POSITIONS_IMPORT_FORM_PROGRESS_BAR_CAPTION') ?></div>
    <div id="content"></div>
    <form action="<?= POST_FORM_ACTION_URI ?>" method="POST" id="import-form">

        <? $APPLICATION->IncludeComponent("bitrix:main.file.input", "",
          array(
            "INPUT_NAME" => "NEW_FILE_UPLOAD",
            "MULTIPLE" => "N",
            "MODULE_ID" => "iblock",
            "MAX_FILE_SIZE" => "",
            "ALLOW_UPLOAD" => "F",
            "ALLOW_UPLOAD_EXT" => 'xlsx, xls',
            "INPUT_VALUE" => $_POST['NEW_FILE_UPLOAD']
          ),
          false

        ); ?>

        <input type="submit" id="import-button" value=<?= Loc::getMessage('YLAB_POSITIONS_IMPORT_FORM_BUTTON_TITLE') ?>>
    </form>

<? endif; ?>

<?php
$file_id = $_POST['NEW_FILE_UPLOAD'];
$path = $_SERVER['DOCUMENT_ROOT'] . (CFile::getPath($file_id));
?>


<script>
    button = BX('import-button');
    content = BX('content');
    progress_bar = BX('progress-bar');

    BX.bind(button, 'click', ()=> {
        repeat_import();
    });

    function repeat_import() {
        BX.ajax.runComponentAction('ylab:positions.import',
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
                    content.innerText = <?='"' . Loc::getMessage('YLAB_POSITIONS_IMPORT_FORM_FINISHED') . '"'?>;

                } else if (response.data === 'not_right_extension') {
                    console.log(response);
                    content.innerText = <?='"' . Loc::getMessage('YLAB_POSITIONS_IMPORT_FORM_WRONG_EXTENSION') . '"'?>;
                } else {
                    content.innerText = response;
                    repeat_import();
                }
            }, function (response) {
                //сюда будут приходить все ответы, у которых status !== 'success'
                console.log(response);
                progress_bar.append("I");
                repeat_import();
            });
    }
</script>