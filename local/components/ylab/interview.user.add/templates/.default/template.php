<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
if (!empty($arResult["ERRORS"])) {
    ?>
    <div class="user_add_form_errors"><?= $arResult['ERRORS'] ?></div>
    <br>
    <?php
}
if (!empty($arResult['SUCCESS'])) {
    ?>
    <div class="user_add_form_success"><?= $arResult['SUCCESS'] ?></div>
    <br>
    <br>
    <a href="<?= $arResult['LINK'] ?>"><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_FORM_NEW_USER') ?></a>
    <?php
} else {
    ?>

    <form id="user_add_form" class="form user_add_form" action="<?= $APPLICATION->GetCurPage() ?>" method="post">
        <?= bitrix_sessid_post() ?>
        <h3><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_FORM') ?></h3>
        <div class="user_add_form_row">
            <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_NAME') ?></label>
            <input name="NAME" type="text" value="" required>
        </div>
        <div class="user_add_form_row">
            <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_SURNAME') ?></label>
            <input name="SURNAME" type="text" value="" required>
        </div>
        <div class="user_add_form_row">
            <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_LASTNAME') ?></label>
            <input name="LASTNAME" type="text" value="" required>
        </div>
        <div class="user_add_form_row">
            <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_POST') ?></label>
            <input name="POST" type="text" value="" required>
        </div>
        <div class="user_add_form_row">
            <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_COMPANY') ?></label>
            <input name="COMPANY" type="text" value="" required>
        </div>
        <div class="user_add_form_row">
            <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_PASSPORT') ?></label>
            <input id="passport" name="PASSPORT" type="text" value="" required>
        </div>
        <script type="text/javascript">
            BX.ready(function () {
                BX.UserAddForm.create('user_add_form', {
                    post: {
                        passport: BX('passport').value,
                        iblock_id: <?= CUtil::PHPToJsObject($arResult["iblock_id"])?>
                    }
                });
            });
            BX.bind(BX('passport'), 'input', function () {
                if (this.value) {
                    BX('btn__check_password').removeAttribute('disabled');
                } else {
                    BX('btn__check_password').setAttribute('disabled', true);
                }
            });
        </script>
        <div class="user_add_form_row">
            <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_CHECK_PASSPORT') ?></label>
            <input type="button" id="btn__check_password" class="btn__check_password"
                   value="<?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_CHECK_PASSPORT_BUTTON') ?>" disabled>
        </div>
        <div class="" id="btn__check_password_message"></div>
        <br><br>
        <input type="submit" value="<?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_BUTTON') ?>">
    </form>
    <?php
}
?>