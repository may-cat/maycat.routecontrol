<?
namespace Maycat\Routecontrol;
class CSiteStructureController
{
    static function getStructure($SITE_ID)
    {
        global $resp; // Будет использоваться в routecontrol:sitemap
        global $arrAllMenus;
        global $APPLICATION;
        $arrAllMenus=array(
            0=>array('top'), /// @todo: вынести в настройки компонента
            1=>array('left'),
            2=>array('leftsub'),
        );
        $APPLICATION->IncludeComponent("routecontrol:sitemap", "array_export_plain", Array(
                'FORCE_LANG'=>$SITE_ID,
                'VAR_NAME'=>'arrAllMenus',
                'VAR_RESULT'=>'resp',
                "CACHE_TYPE" => "N",	// Тип кеширования
                "CACHE_TIME" => "0",	// Время кеширования (сек.)
                "SET_TITLE" => "N",	// Устанавливать заголовок страницы
                "LEVEL" => "7",	// Максимальный уровень вложенности (0 - без вложенности)
                "COL_NUM" => "2",	// Количество колонок
                "SHOW_DESCRIPTION" => "Y",	// Показывать описания
            ),
            false
        );
        if (! count($resp))
            $arErrors[]='Не загружено дерево разделов. Возможно в настройках у сайта не указан путь к корневой директории. Для работы модуля это критично.'; // @todo: Как-то это надо вернуть куда-то наружу!

        return $resp;
    }

    static function getFirstChildUrl($url,$SITE_ID)
    {
        $regexpUrl = preg_quote($url,'/');
        $arStruct = self::getStructure($SITE_ID);

        // Ищем текущую страницу
        for ($i=0,$found=false;$i<count($arStruct) && !$found;$i++)
        {
            if (preg_match("/^$regexpUrl/",$arStruct[$i]['path']))
                $found = true;
        }
        // Ищем следующую разрешённую
        for ($found=false;preg_match("/^$regexpUrl/",$arStruct[$i]['path']) && !$found;$i++)
        {
            if (RCMenuLockTable::isValidUrl($arStruct[$i]['path'],$SITE_ID))
                return $arStruct[$i]['path'];
        }
        return null;
    }
}