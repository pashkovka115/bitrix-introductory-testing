<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<?php
use Bitrix\Main\Localization\Loc;
?>


<? if (!empty($arParams['POSITIONS_HL_NAME']) && !empty($arParams['ORGANIZATIONS_HL_NAME'])) : ?>
    <h3 class=""><?=Loc::getMessage('YLAB_POSITIONS_IMPORT_FORM_TITLE')?></h3>
    <div class="">
        <form enctype="multipart/form-data" method="post" action="<?= POST_FORM_ACTION_URI ?>">
            <input type="hidden" name="action" value="import_xlsx">

            <p><input class="" type="file" name="file-upload">
                <input class="" type="submit" value="<?=Loc::getMessage('YLAB_POSITIONS_IMPORT_FORM_BUTTON_TITLE')?>"></p>
        </form>

    </div>
<? endif; ?>







