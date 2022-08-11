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
    <a href="<?= $arResult['LINK'] ?>">Добавить нового пользователя</a>
    <?php
} else {
    ?>

    <form class="form user_add_form" action="<?= $APPLICATION->GetCurPage() ?>" method="post">
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
            <input name="PASSPORT" type="text" value="" required>
        </div>
        <br><br>
        <input type="submit" value="<?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_USER_ADD_BUTTON') ?>">
    </form>
    <?php
}
?>