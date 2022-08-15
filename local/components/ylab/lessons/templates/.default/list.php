<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;


usort($arResult['LESSONS'], function ($a, $b) {
    return $a['IS_COMPLETED'] <=> $b['IS_COMPLETED'];
});
?>

<div class="lessons-list">
    <?php
    /** @var array $arResult */
    foreach ($arResult['LESSONS'] as $arItem) {
    ?>
        <p class="lessons-list" id="">
            Название урока: <?= $arItem['NAME'] ?>
        </p>
        <img src="<?= $arItem['IMAGE'] ?>" width="200" height="150" alt="<?= '213123' ?>" />
        <p class="lessons-list" id="">
            <?= $arItem['IS_COMPLETED'] ? 'Курс пройден' : 'Курс не пройден' ?>
        </p>
        <a href="/lessons/view/<?= $arItem['ID'] ?>/"><?= Loc::getMessage('DETAILS') ?></a>
        <hr>
</div>
<?php } ?>