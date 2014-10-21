<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once(substr(__DIR__, 0, strrpos(__DIR__, '/'))."/prolog.php");
\Bitrix\Main\Loader::IncludeModule(ADMIN_MODULE_NAME);

$MOD_RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);

if($MOD_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $MOD_RIGHT >= "W";
$arErrors = array();
if (! $isAdmin)
    $arErrors[]='Вы не сможете сохранять данные, так как у Вас нет на это прав.';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Список сайтов

$resSiteController = new \Maycat\Routecontrol\CSitesListController();
$SITE_ID = $resSiteController->getSiteId();
$URL = $resSiteController->getSiteUrl();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Читаем данные о структуре сайта
$resp = \Maycat\Routecontrol\CSiteStructureController::getStructure($SITE_ID);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Сохранение

if ($isAdmin && $_REQUEST['form_sent']=='Y')
    try
    {
        // Чистим старое
        \Maycat\Routecontrol\RCMenuLockTable::clear($SITE_ID);
        \Maycat\Routecontrol\RCAccessLockTable::clear($SITE_ID);
        // Сохраняем новое
        $arMenuLockItems = $_REQUEST['menu_lock'];
        $arAccessLockItems = $_REQUEST['access_lock'];
        //call_user_func
        foreach ($arMenuLockItems as $iElement)
        {
            \Maycat\Routecontrol\RCMenuLockTable::add(array(
                'PATH'=>$resp[$iElement-1]['path'],
                'SITE_ID'=>$SITE_ID
            ));
        }
        foreach ($arAccessLockItems as $iElement)
        {
            \Maycat\Routecontrol\RCAccessLockTable::add(array(
                'PATH'=>$resp[$iElement-1]['path'],
                'SITE_ID'=>$SITE_ID
            ));
        }
    }
    catch (Exception $e){
        $arErrors[]=$e->getMessage();
    }


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Чтение актуального списка

$res = \Maycat\Routecontrol\RCAccessLockTable::getList(array('filter'=>array('SITE_ID'=>$SITE_ID)));
$arChosen = $res->fetchAll();
foreach ($arChosen as $elChosen)
{
    foreach ($resp as &$el)
        if ($el['path']==$elChosen['PATH'])
            $el['access_lock']=true;
}
$res = \Maycat\Routecontrol\RCMenuLockTable::getList(array('filter'=>array('SITE_ID'=>$SITE_ID)));
$arChosen = $res->fetchAll();
foreach ($arChosen as $elChosen)
{
    foreach ($resp as &$el)
        if ($el['path']==$elChosen['PATH'])
            $el['menu_lock']=true;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$APPLICATION->SetTitle(GetMessage("TITLE"));
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>
<p>Модуль поможет скрыть любые ссылки в меню, но только если оные меню выводятся через компоненты модуля.</p>
<? if (count($arErrors)):?>
    <? foreach ($arErrors as $sError) ShowError($sError); ?>
<? endif ?>
    <?=$resSiteController->echoSiteSelect()?>

    <form method="post">
    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="list-table">
        <tr class="heading">
            <td>Прятать из меню</td>
            <td>Закрыть доступ по ссылке</td>
            <td>Адрес</td>
            <td>Название</td>
        </tr>

        <? /* --------------------------------------------------------------------------------------- */ ?>
        <? function showRow($item) {?>
            <tr>
                <td><input type="checkbox" value="<?=$item['id']?>" id="menu_lock_<?=$item['id']?>" name="menu_lock[]" <?=$item['menu_lock']?'checked="checked"':''?> /></td>
                <td><input type="checkbox" value="<?=$item['id']?>" id="access_lock_<?=$item['id']?>" name="access_lock[]" <?=$item['access_lock']?'checked="checked"':''?> /></td>
                <td><label for="menu_lock_<?=$item['id']?>">
                        <? if (preg_match('/^http\:\/\//',$item['path'])): ?>
                            <?=$item['path']?> (внешняя ссылка)
                        <? else: ?>
                            <a href="<?=$GLOBALS['URL']?><?=$item['path']?>"><?=$item['path']?></a>
                        <? endif ?>
                    </label></td>
                <td><?=$item['name']?></label></td>
            </tr>
        <? } ?>
        <? /* --------------------------------------------------------------------------------------- */ ?>

        <? foreach ($resp as $item): ?>
            <? showRow($item) ?>
        <? endforeach ?>
    </table>
    <div class="adm-list-table-footer" id="tbl_vote_channel_footer_edit" style="left: 0px;">
        <input type="hidden" value="Y" name="form_sent"/>
        <input type="submit" class="adm-btn-save" name="save" value="Сохранить" title="Сохранить изменения">
    </div>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
