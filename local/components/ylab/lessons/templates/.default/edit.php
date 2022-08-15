<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<?php
/** @var array $arResult */
if (isset($arResult['LESSON_DATA']['ID'])) {
    if (!$arResult['LESSON_DATA']['IS_COMPLETED']) {
        ?>
        <?= Loc::getMessage('TITLE') ?>
        <form action="" method="post">
            <p><input name="option" type="radio" value="1"> <?= Loc::getMessage('1ST_OPTION') ?></p>
            <p><input name="option" type="radio" value="2"> <?= Loc::getMessage('2ND_OPTION') ?></p>
            <p><input type="submit" value="<?= Loc::getMessage('FINISH_TEST') ?>"></p>
        </form>
        <?php
    } else {
        echo Loc::getMessage('TEST_WAS_PASSED');
    }
} else {
    echo Loc::getMessage('LESSON_NOT_FOUND');
} ?>