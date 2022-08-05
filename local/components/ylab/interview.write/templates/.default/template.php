<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;

/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var \CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var array $templateData */
/** @var \CBitrixComponent $component */
$this->setFrameMode(true);

?>
<div class="slots"> <?php
    foreach ($arResult['SLOTS'] as $day => $slots) {
        ?> <h3><?= $day ?></h3>
        <?php
        foreach ($slots as $slot) { ?>
            <?php
            if ($slot['SLOT']['FREE'] == 'Y') {
                $class = ' free';
            } elseif ($slot['SLOT']['FREE'] == 'N') {
                $class = ' busy';
            } ?>
          <span <?php if ($slot['SLOT']['FREE'] == 'N'){?>title="<?= $slot['NAME'] ?>" <?php } ?>
              class="slot<?= $class ?>"><?= (new \Bitrix\Main\Type\DateTime($slot['SLOT']['VALUE']))->format('H:i') ?></span> <?php
        }
    }
    ?></div>

<form class="form" action="<?= $APPLICATION->GetCurPage() ?>" method="post">
  <?= bitrix_sessid_post() ?>
  <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_BOOK_TO_TIME') ?><br>
    <select name="SLOT_DATETIME">
        <?php
        foreach ($arResult['SLOTS'] as $day => $slots) {
            foreach ($slots as $slot) { ?>
                <?php
                if ($slot['SLOT']['FREE'] == 'Y') {
                    $disabled = '';
                } elseif ($slot['SLOT']['FREE'] == 'N') {
                    $disabled = ' disabled';
                } ?>
              <option
                  value="<?= $slot['SLOT']['VALUE'] ?>"<?= $disabled ?>><?= (new \Bitrix\Main\Type\DateTime($slot['SLOT']['VALUE']))->format('d.m.Y H:i') ?></option> <?php
            }
        }
        ?></select>
  </label>
  <br><br>
  <label><?= Loc::getMessage('YLAB_INTERVIEW_TEMPLATE_BOOK_TO_TIME_FOR_USER') ?><br>
    <select name="USER">
      <?php foreach ($arResult['USERS'] as $user) { ?>
            <option value="<?= $user['ID'] ?>"><?= $user['NAME'] ?></option>
      <?php } ?>
    </select>
  </label><br><br>
  <input type="submit" value="Забронировать">
</form>


