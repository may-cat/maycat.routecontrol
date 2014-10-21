<?
namespace Maycat\Routecontrol;

class CSitesListController
{
    private $arSites;
    private $site_id;
    private $url;

    function __construct()
    {
        $arSites = array();
        $this->site_id = '';
        $rs = \Bitrix\Main\SiteTable::getList();
        while ($tmp = $rs->fetch())
        {
            if ($_REQUEST['SITE_ID'] && $_REQUEST['SITE_ID']==$tmp['LID'])
            {
                $tmp['selected'] = true;
                $this->site_id = $tmp['LID'];
                $this->url = $tmp['SERVER_NAME'];
            }
            elseif (!$_REQUEST['SITE_ID'] && $tmp['DEF']=='Y')
            {
                $tmp['selected'] = true;
                $this->site_id = $tmp['LID'];
                $this->url = $tmp['SERVER_NAME'];
            }
            $arSites[$tmp['LID']] = $tmp;
        }
        $this->arSites = $arSites;

    }


    function getSiteId()
    {
        return $this->site_id;
    }

    function getSiteUrl()
    {
        return "http://".$this->url;
    }


    function echoSiteSelect()
    {
        ob_start();
        ?>
        <select id="SITE_ID_CHOSER" onchange="document.location.href='<?=$GLOBALS['APPLICATION']->GetCurPage(false)?>?SITE_ID='+this.value;">
            <? foreach ($this->arSites as $arSite): ?>
                <option value="<?=$arSite['LID']?>" <?=($arSite['selected'])?'selected="selected"':''?>>[<?=$arSite['LID']?>] <?=$arSite['NAME']?></option>
            <? endforeach?>
        </select>
        <?
        $html = ob_get_contents(); ob_end_clean();
        return $html;
    }

}