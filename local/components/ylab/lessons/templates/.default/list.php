<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

?>

    <div class="lessons-list">
 <?php
    /** @var array $arResult */
   foreach ($arResult['LESSONS'] as $arItem) {
   ?>
<p class="lessons-list" id="">
 Название урока: <?= $arItem['NAME'] ?>
 </p>
 <img src="<?= CFile::GetFileArray($arItem['IMAGE'])['SRC'] ?>" width="200" height="150" alt="<?= '213123' ?>"/>
  <p class="lessons-list" id="">
 <?= $arItem['IS_COMPLETED']? 'Курс не пройден':'Курс пройден' ?>
 </p>
 <hr>
 <?php } ?>