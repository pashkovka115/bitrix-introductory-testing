<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){
    die();
}
use Bitrix\Main\Localization\Loc;
?>

<?if ( $arResult["NEW_QUESTIONS"] == 'y') : ?>
<h2><?=Loc::getMessage("YLAB_TESTING_COUNT_TRUE_ANSWER")?><?=$arResult["COUNT_TRUE_ANSWER"]?></h2>

<? foreach ($arResult["QUESTION_LIST"] as $key => $arItem) : ?>
    <h2><?=($arItem["NAME"])?></h2>
    <? foreach($arItem["ANSWER"] as $itemAnswer) : ?>
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
        <?$countQuestion = 1;?>
        <? foreach ($arResult["QUESTION_LIST"] as $key => $arItem) :?>
            <input type="hidden" name="id<?=$countQuestion?>" value=<?=$key?>>
            <?$countQuestion += 1;?>

            <?if ($arResult["EMPTY_ANSWER"] == "y") :?>
                <h2><?=$arItem["NAME"]?></h2>

                <?if (empty($arResult["ANSWERS_USER"][$key])) :?>
                <h2 style="color:red"><?=Loc::getMessage("YLAB_TESTING_ANSWER_REQUIRED")?></h2>
                <?endif;?>
                <?foreach($arItem["ANSWER"] as $itemAnswer) : ?>

                    <?if ($arItem["TYPE_QUESTION"] == "text" and isset($arResult["ANSWERS_USER"][$key])) :?>
                        <label><input type="<?=$arItem["TYPE_QUESTION"]?>" placeholder="<?=$arResult["ANSWERS_USER"][$key]?>"
                                      name="<?=$key?>" value="<?=$arResult["ANSWERS_USER"][$key]?>"><?=$itemAnswer?>
                        </label><p></p>
                    <?elseif ($arResult["ANSWERS_USER"][$key] == $itemAnswer) :?>
                        <label><input type="<?=$arItem["TYPE_QUESTION"]?>" name="<?=$key?>" value="<?=$itemAnswer?>"
                                      checked><?=$itemAnswer?></label><p></p>
                    <?else:?>
                        <label><input type="<?=$arItem["TYPE_QUESTION"]?>" name="<?=$key?>" value="<?=$itemAnswer?>">
                            <?=$itemAnswer?></label><p></p>
                    <?endif;?>
                <? endforeach; ?>
            <?else:?>
                <h2><?=$arItem["NAME"]?></h2>
                <? foreach($arItem["ANSWER"] as $itemAnswer) : ?>
                    <label><input type="<?=$arItem["TYPE_QUESTION"]?>" name="<?=$key?>" value="<?=$itemAnswer?>">
                        <?=$itemAnswer?></label><p></p>
                <? endforeach; ?>
            <?endif;?>
        <? endforeach; ?>
        <button type="submit" name="newQuestions" value="n"><?=Loc::getMessage("YLAB_TESTING_SEND_BUTTON")?></button>
    </form>
<?endif;?>

