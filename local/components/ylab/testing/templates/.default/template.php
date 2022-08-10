<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){
    die();
}
use Bitrix\Main\Localization\Loc;
?>

<?if ( $arResult["NEW_QUESTIONS"] == 'y') : ?>
<h2><?=Loc::getMessage("YLAB_TESTING_COUNT_TRUE_ANSWER")?><?=$arResult["COUNT_TRUE_ANSWER"]?></h2>

<? foreach ($arResult["QUESTION_LIST"] as $key => $arItem) : ?>
    <h2><?=($arItem["NAME"])?></h2>
    <? foreach($arItem["ANSWER"] as $itemAnswer) : ;?>
        <?if ($itemAnswer == $arItem["TRUE_ANSWER"] or $itemAnswer == null) : ?>
            <h3 style="color:green"><?=$arItem["TRUE_ANSWER"]?></h3>
        <?else:?>
                <p><?=$itemAnswer?></p>
                <p></p>
        <?endif;?>
    <? endforeach; ?>
<? endforeach; ?>
<form action="" method="post">
    <button type="submit" name="newQuestions" value="y"><?=Loc::getMessage("YLAB_TESTING_TAKE_TEST_AGAIN")?></button>
</form>

<?else:?>
    <form action="" method="post">
        <? foreach ($arResult["QUESTION_LIST"] as $key => $arItem) : ?>
            <h2><?=($arItem["NAME"])?></h2>
            <? foreach($arItem["ANSWER"] as $itemAnswer) : ?>
                <input type=<?=$arItem["TYPE_QUESTION"]?> id="<?=$itemAnswer?>" name="<?=$key?>" value="<?=$itemAnswer?>">
                <label for="<?=$itemAnswer?>"><?=$itemAnswer?></label><p></p>
            <? endforeach; ?>
        <? endforeach; ?>
        <button type="submit" name="newQuestions" value="n"><?=Loc::getMessage("YLAB_TESTING_SEND_BUTTON")?></button>

    </form>
<?endif;?>

