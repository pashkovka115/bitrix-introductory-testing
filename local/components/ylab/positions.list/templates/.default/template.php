<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<?php

use Bitrix\Main\Localization\Loc;

?>

<? if (!$arResult['IS_HL_MODULE_INCLUDED']) : ?>

    <?= Loc::getMessage('YLAB_POSITIONS_LIST_TABLE_DEFAULT_TEMPLATE_ERROR1') ?>
    <?= '<br>' ?>

<? else: ?>

    <? if (empty($arParams['POSITIONS_HL_NAME'])) : ?>
        <?= Loc::getMessage('YLAB_POSITIONS_LIST_TABLE_DEFAULT_TEMPLATE_ERROR2') ?>
        <?= '<br>' ?>
    <? endif; ?>
    <? if (empty($arParams['ORGANIZATIONS_HL_NAME'])) : ?>
        <?= Loc::getMessage('YLAB_POSITIONS_LIST_TABLE_DEFAULT_TEMPLATE_ERROR3') ?>
        <?= '<br>' ?>
    <? endif; ?>


    <? if (!empty($arParams['POSITIONS_HL_NAME']) && !empty($arParams['ORGANIZATIONS_HL_NAME'])) : ?>
        <div class="">
            <h3>
                <?= Loc::getMessage('YLAB_POSITIONS_LIST_TABLE_DEFAULT_TEMPLATE_PREFIX') ?>
                <?= $arResult['ITEMS']['GRID_NAME'] ?>
            </h3>
            <p></p>

            <table border="1" width="100%" cellpadding="5">

                <tr>
                    <? foreach ($arResult['ITEMS']['GRID_HEAD'] as $arItem) : ?>
                        <th><?= $arItem['name'] ?></th>
                    <? endforeach; ?>
                </tr>

                <? foreach ($arResult['ITEMS']['ELEMENTS'] as $arItem) : ?>
                    <tr>
                        <? foreach ($arItem as $key => $value) : ?>
                            <?= '<td>' ?><?= $value ?><?= '</td>' ?>
                        <? endforeach ?>
                    </tr>
                <? endforeach; ?>
            </table>

        </div>
    <? endif; ?>

<? endif; ?>

