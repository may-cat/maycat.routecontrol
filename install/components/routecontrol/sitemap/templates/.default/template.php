<?
/*
 *  Предупреждение:
 *  Из-за оригинальной структуры сайта и не менее оригинального макета в коде ниже содержится критический объём шаманизма.
 *  Good luck.
 */


if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!is_array($arResult["arMap"]) || count($arResult["arMap"]) < 1)
	return;

$arRootNode = Array();
foreach($arResult["arMap"] as $index => $arItem)
{
	if ($arItem["LEVEL"] == 0)
		$arRootNode[] = $index;
}
$SomeIteratorCounter=0;
$allNum = count($arRootNode);
$colNum = ceil($allNum / $arParams["COL_NUM"]);

?>
<div class="sitemap">
    <div class="column">
		<?
		$previousLevel = -1;
		$counter = 0;
		$column = 1;
		foreach($arResult["arMap"] as $index => $arItem):?>

			<?if ($arItem["LEVEL"] < $previousLevel):?>
				<?=str_repeat("</ul></li>", ($previousLevel - $arItem["LEVEL"]));?>
			<?endif?>


			<?if ($arItem["LEVEL"] == 0 && $SomeIteratorCounter>0 && $SomeIteratorCounter<3):
                    $SomeIteratorCounter++;
					$column++;
			?>
    </div><!-- /.column -->
    <div class="column">
        <? if ($arItem['LEVEL']==0 && $SomeIteratorCounter>=3): ?>
            <ul class="map-level-0">
        <? endif ?>
			<?elseif ($arItem['LEVEL']==0):
                $SomeIteratorCounter++;
            endif?>

			<?if (array_key_exists($index+1, $arResult["arMap"]) && $arItem["LEVEL"] < $arResult["arMap"][$index+1]["LEVEL"]):?>

                <? if ($arItem['LEVEL']==0 && $SomeIteratorCounter<3): ?>
                    <h4>
                        <a href="<?=$arItem["FULL_PATH"]?>"><?=$arItem["NAME"]?></a><?if ($arParams["SHOW_DESCRIPTION"] == "Y" && strlen($arItem["DESCRIPTION"]) > 0) {?><div><?=$arItem["DESCRIPTION"]?></div><?}?>
                    </h4>
                <? else: ?>
                    <li>
                        <a href="<?=$arItem["FULL_PATH"]?>"><?=$arItem["NAME"]?></a><?if ($arParams["SHOW_DESCRIPTION"] == "Y" && strlen($arItem["DESCRIPTION"]) > 0) {?><div><?=$arItem["DESCRIPTION"]?></div><?}?>
                <? endif?>
                    <ul class="map-level-<?=$arItem["LEVEL"]+1?>">
			<?else:?>
					<li><a href="<?=$arItem["FULL_PATH"]?>"><?=$arItem["NAME"]?></a><?if ($arParams["SHOW_DESCRIPTION"] == "Y" && strlen($arItem["DESCRIPTION"]) > 0) {?><div><?=$arItem["DESCRIPTION"]?></div><?}?></li>
			<?endif?>
			<?
				$previousLevel = $arItem["LEVEL"];
				if($arItem["LEVEL"] == 0)
					$counter++;
			?>
		<?endforeach?>

		<?if ($previousLevel > 1)://close last item tags?>
            <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
		<?endif?>

    </div>
</div>