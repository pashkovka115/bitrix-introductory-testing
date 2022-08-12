<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
Тестирование
<div class="questions">
    <li>
        <ol>1.</ol>
        <ol>2.</ol>
        <ol>3.</ol>
    </li>
    <button type="submit"><?= Loc::getMessage('FINISH_TEST') ?></button>
</div>
