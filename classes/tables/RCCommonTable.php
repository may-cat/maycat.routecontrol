<?
namespace Maycat\Routecontrol;

/**
 * Class CRoutecontrolUrlsTable
 * @package Maycat\Routecontrol
 * Общий класс, который необходимо наследовать
 */
class RCCommonTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    { } // Нужно экранировать

    public static function getTableName()
    { } // Нужно экранировать

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
            ),
            'PATH' => array(
                'data_type' => 'string',
                'title' => 'Регулярное выражение',
                'required' => true,
            ),
            'SITE_ID' => array(
                'data_type' => 'string',
                'title' => 'ID сайта',
                'required' => true,
            ),
        );
    }

    /**
     * @param $url
     * @return bool
     * Проверяет, есть ли у юзера доступ к этому урлу.
     */
    static function isValidUrl($url,$site_id = null)
    {
        $site_id = self::restoreSiteId($site_id); // восстанавливаем $site_id, если не задан
        // В ряде сценариев мы сразу пускаем на все урлы
        $T = get_called_class();
        if ($T::hasRightToSeeEverything())
            return true;
        // Обрабатываем урл
        global $APPLICATION;
        $arCurPage = explode('/',$url);
        $sCurPage = $url;
        do {
            // Пробуем найти указанный урл в базе
            $rs = $T::getList(array('filter'=>array(
                'PATH' => $sCurPage,
                'SITE_ID' => $site_id
            )));
            // Если есть - значит урл наш не валиден
            $row = $rs->fetch();
            if ($row)
                return false;
            // А если нет - разбираем урл на уровень выше
            array_pop($arCurPage);
            $sCurPage = implode('/',$arCurPage).'/';
        } while (count($arCurPage));
        return true;
    }

    /**
     * @param null $site_id
     * Вычищает все адреса, относящиеся к указанному сайту
     */
    static function clear($site_id=null)
    {
        $site_id = self::restoreSiteId($site_id); // восстанавливаем $site_id, если не задан
        $res = self::getList(array('filter'=>array('SITE_ID'=>$site_id)));
        $ar = $res->fetchAll();
        foreach ($ar as $i)
            self::delete($i['ID']);
    }


    /**
     * проверка корректности SITE_ID. Если некорректен - надо брать первый или дефолтный.
     */
    protected static function restoreSiteId($site_id)
    {
        if ($site_id)
        {
            $rsSite = \Bitrix\Main\SiteTable::getList(array('filter'=>array('LID'=>$site_id),'select'=>array('LID')));
            $arSites = $rsSite->fetch();
            if (! $arSites['LID'])
                $site_id = null;
        }
         if (! $site_id)
         {
             $rsSite = \Bitrix\Main\SiteTable::getList(array('filter'=>array('DEF'=>'Y'),'select'=>array('LID')));
             $arSites = $rsSite->fetch();
             $site_id = $arSites['LID'];
         }
        return $site_id;
    }

    /**
     * @return bool
     */
    protected static function hasRightToSeeEverything()
    {
        global $APPLICATION;
        $MOD_RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);

        if($MOD_RIGHT == "D")
            return false;

        return true;
    }
}