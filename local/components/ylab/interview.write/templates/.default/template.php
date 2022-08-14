<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Type\DateTime;


/**
 * @var array $arParams
 * @var array $arResult
 * @global \CMain $APPLICATION
 * @global \CUser $USER
 * @global \CDatabase $DB
 * @var \CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var array $templateData
 * @var \CBitrixComponent $component
 */

$this->setFrameMode(true);

?>
<div id="slots_interview.write" class="slots"> <?php
    foreach ($arResult['SLOTS'] as $day => $slots) {
        ?> <h3><?= $day ?></h3>
        <?php
        foreach ($slots as $slot) { ?>
            <?php
            if ($slot['SLOT']['FREE'] == 'Y') {
                $class = ' free';
            } elseif ($slot['SLOT']['FREE'] == 'N') {
                $class = ' busy';
            } else {
                $class = ' not-user';
            } ?>
          <span data-slot="<?= $slot['SLOT']['VALUE'] ?>"
                data-iblock="<?= $slot['IBLOCK_ID'] ?>"
                 <?php
                 if ($slot['SLOT']['FREE'] == 'N') { ?>
                   data-element="<?= $slot['ELEMENT_ID'] ?>"
                   data-property="<?= $slot['PROPERTY_ID'] ?>"
                   title="<?= $slot['NAME'] ?>"
                     <?php
                 } ?>
              class="slot<?= $class ?>"><?= (new DateTime($slot['SLOT']['VALUE']))->format('H:i') ?></span> <?php
        }
    }
    ?></div>

<script>
    BX.ready(function () {
        BX.YlabSlots.create('slots_interview.write', {
            users: <?= CUtil::PHPToJsObject($arResult['USERS'])?>
        });
    });
</script>
