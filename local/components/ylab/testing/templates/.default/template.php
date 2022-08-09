<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){
    die();
}
use Bitrix\Main\Localization\Loc;

?>

<?if ( $arResult["RESULTING_PAGE"] == "y") : ?>
<h2><?=Loc::getMessage("YLAB.TESTING.COUNT.TRUE.ANSWER")?><?=$arResult["COUNT_TRUE_ANSWER"]?></h2>

<? foreach ($arResult["RANDOM_LIST"] as $key => $arItem) : ?>
    <h2><?=($arItem["NAME"])?></h2>
    <? foreach($arItem["ANSWER"] as $itemAnswer) : ?>
        <?if ($itemAnswer == $arItem["TRUE_ANSWER"]) : ?>
            <h3 style="color:green"><?=$arItem["TRUE_ANSWER"]?></h3>
        <?else:?>
                <p><?=$itemAnswer?></p>
                <p></p>
        <?endif;?>
    <? endforeach; ?>
<? endforeach; ?>
<form action="" method="post">
    <button type="submit" name="redirect" value="y"><?=Loc::getMessage("YLAB.TESTING.TAKE.TEST.AGAIN")?></button>
</form>

<?else:?>
    <form action="" method="post">
        <? foreach ($arResult["RANDOM_LIST"] as $key => $arItem) : ?>
            <h2><?=($arItem["NAME"])?></h2>
            <? foreach($arItem["ANSWER"] as $itemAnswer) : ?>
                <input type="checkbox" id="<?=$itemAnswer?>" name="<?=$key?>" value="<?=$itemAnswer?>">
                <label for="<?=$itemAnswer?>"><?=$itemAnswer?></label><p></p>
            <? endforeach; ?>
        <? endforeach; ?>
        <input type="submit" value="<?=Loc::getMessage("YLAB.TESTING.SEND.BUTTON")?>">
    </form>
<?endif;?>

