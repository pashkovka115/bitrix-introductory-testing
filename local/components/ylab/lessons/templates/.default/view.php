<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

?>
    <div class="lesson-data">
<?php
/** @var array $arResult */
if (isset($arResult['LESSON_DATA']['ID'])) {
    ?>
    <p class="lesson-data" id="">
        Название урока: <?= $arResult['LESSON_DATA']['NAME'] ?>
    </p>
    <img src="<?= CFile::GetFileArray($arResult['LESSON_DATA']['IMAGE'])['SRC'] ?>" width="200" height="150"
         alt="<?= '213123' ?>"/>
    <p class="lesson-data" id="">
        Описание: <?= $arResult['LESSON_DATA']['DESCRIPTION'] ?>
    </p>
    <?php if (!$arResult['LESSON_DATA']['IS_COMPLETED']) { ?>
        <a href="/lessons/edit/<?= $arResult['LESSON_DATA']['ID'] ?>/"><?= Loc::getMessage('START_TEST') ?></a>
        </div>
    <?php }
} else {
    echo Loc::getMessage('LESSON_NOT_FOUND');
} ?>