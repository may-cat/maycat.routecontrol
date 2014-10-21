<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$GLOBALS["arrMainMenu"] = explode(",",COption::GetOptionString("main","map_top_menu_type","top")); // @todo: вынести в настройки модуля!
$GLOBALS["arrChildMenu"] = explode(",",COption::GetOptionString("main","map_left_menu_type","left"));// @todo: вынести в настройки модуля!
$GLOBALS["arrSearchPath"] = array();

$arParams["LEVEL"] = intval($arParams["LEVEL"]);
$arParams["COL_NUM"] = intval($arParams["COL_NUM"]);
if ($arParams["LEVEL"] < 0) $arParams["LEVEL"] = 0;
if ($arParams["COL_NUM"] <= 0) $arParams["COL_NUM"] = 1;

if (!is_set($arParams, "CACHE_TIME")) $arParams["CACHE_TIME"] = "14400";

$arParams["SHOW_DESCRIPTION"] = $arParams["SHOW_DESCRIPTION"] == "N" ? "N" : "Y";

global $arrAllMenus,$arrMainMenu,$arrChildMenu;

if (! $arParams['VAR_NAME'] || ! $GLOBALS[$arParams['VAR_NAME']])
    $arrAllMenus=array(
        0=>$arrMainMenu,
        1=>$arrChildMenu
    );
else{
    $arrAllMenus = $GLOBALS[$arParams['VAR_NAME']];
}


if (!function_exists('GetTree'))
{
    function GetTree($dir, $max_depth, $get_description = false)
    {
        $arMap = GetTreeRecursive('/'.trim($dir,'/'), '/', 0, 0, $max_depth, $get_description);

        return $arMap;
    }
}

if (!function_exists('GetTreeRecursive'))
{
    // Рекурсивно проходимся и строим дерево
    function GetTreeRecursive($ROOT, $PARENT_PATH, $level, $fake_level, $max_depth, $get_description = false)
    {
        global $arrAllMenus, $arrSearchPath, $APPLICATION, $USER;

        static $arIndexes = false;
        if($arIndexes === false)
            $arIndexes = GetDirIndexArray();
        $i = 0;
        $map = array();

        // Выбираем меню, которое соответствует уровню вложенности
        $arrMenu = $arrAllMenus[$level];
        if(is_array($arrMenu) && count($arrMenu)>0)
        {
            foreach($arrMenu as $mmenu)
            {
                $aMenuLinks = array();

                // Формулируем названия файлов, которые будем искать
                $menu_file = ".".trim($mmenu).".menu.php";
                $menu_file_ext = ".".trim($mmenu).".menu_ext.php";

                // Пробуем подключить файлы с меню в текущей папке
                if(file_exists($ROOT.$PARENT_PATH.$menu_file))
                {
                    include($ROOT.$PARENT_PATH.$menu_file);
                    $bExists = true;
                }
                if(file_exists($ROOT.$PARENT_PATH.$menu_file_ext))
                {
                    include($ROOT.$PARENT_PATH.$menu_file_ext);
                    $bExists = true;
                }

                // Если в файлах что-то нашлось - то это надо обработать!
                if ($bExists && is_array($aMenuLinks))
                {
                    // Устанавливаем $parent в null, и, при увеличении уровня выше $parent - складываем в него все последующие элементы
                    // Если уровень становится таким же - обновляем $parent
                    $parent = null;
                    // Проходимся по всем элементам меню
                    foreach ($aMenuLinks as $aMenu)
                    {
                        if (strlen($aMenu[0]) <= 0) continue; // Пропускаем элементы без названий
                        if(count($aMenu)>4) // Для элементов с php-условиями - проверяем эти особые условия
                        {
                            $CONDITION = $aMenu[4];
                            if(strlen($CONDITION)>0 && (!eval("return ".$CONDITION.";")))
                                continue;
                        }

                        $search_child = false;
                        $search_path = '';

                        // Изучаем текущий пункт меню
                        if (strlen($aMenu[1])>0)
                        {
                            if(preg_match("'^(([A-Za-z]+://)|mailto:|javascript:)'i", $aMenu[1]))
                            {
                                $full_path = $aMenu[1];
                            }
                            else
                            {
                                //$full_path = trim(Rel2Abs($ROOT, $aMenu[1]));
                                // если путь $aMenu[1] начинается со слэша - то $PARENT_PATH не нужен!
                                if ($aMenu[1][0]=='/')
                                    $full_path = $aMenu[1];
                                else
                                    $full_path = $PARENT_PATH.$aMenu[1];

                                $slash_pos = strrpos($full_path, "/");
                                if ($slash_pos !== false)
                                {
                                    $page = substr($full_path, $slash_pos+1);
                                    if(($pos = strpos($page, '?')) !== false)
                                        $page = substr($page, 0, $pos);
                                    if($page == '' || $page == 'index.php' || in_array($page, $arIndexes))
                                    {
                                        $search_path = substr($full_path, 0, $slash_pos+1);
                                        $search_child = true;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $full_path = $PARENT_PATH;
                        }

                        // Формулируем результирующий массив
                        if (strlen($full_path)>0)
                        {
                            // Проверяем права доступа, ведь выводить мы можем только если есть права на это!
                            $FILE_ACCESS = (preg_match("'^(([A-Za-z]+://)|mailto:|javascript:)'i", $full_path)) ? "R" : $APPLICATION->GetFileAccessPermission($full_path);
                            if ($FILE_ACCESS!="D" && $aMenu[3]["SEPARATOR"]!="Y")
                            {
                                // Проверяем, является ли это папкой
                                $is_dir = ($search_child && is_dir($ROOT.$search_path)) ? "Y" : "N";
                                if ($is_dir=="Y")
                                {
                                    $search_child &= $level < $max_depth;
                                    $search_child &= !in_array($search_path, $arrSearchPath);
                                }
                                else
                                {
                                    $search_child = false;
                                }

                                //
                                $ar = array();
                                $ar["LEVEL"] = $fake_level;
                                if(isset($aMenu[3]["DEPTH_LEVEL"]) && $aMenu[3]["DEPTH_LEVEL"] > 1)
                                    $ar["LEVEL"] += ($aMenu[3]["DEPTH_LEVEL"] - 1);

                                if($ar["LEVEL"] > $max_depth)
                                    continue;

                                $ar["ID"] = md5($full_path.$ar["COUNTER"]);
                                $ar["IS_DIR"] = is_dir($ROOT.$full_path) ? "Y" : "N";
                                $ar["NAME"] = $aMenu[0];
                                $ar["PATH"] = $PARENT_PATH;
                                $ar["FULL_PATH"] = $full_path;
                                $ar["SEARCH_PATH"] = $search_path;
                                $ar["DESCRIPTION"] = "";
                                $ar['PARAMS']=$aMenu[3];

                                // Если это - раздел, надо попробовать вытянуть дополнительные сведения о папке
                                if ($get_description && $ar["IS_DIR"] == "Y")
                                {
                                    if (file_exists($ROOT.$full_path.".section.php"))
                                    {
                                        $arDirProperties = array();
                                        include($ROOT.$full_path.".section.php");
                                        if($arDirProperties["description"] <> '')
                                            $ar["DESCRIPTION"] = $arDirProperties["description"];
                                        $ar['DIR_PROPS']=$arDirProperties;
                                    }
                                }

                                // Если нужно искать подразделы - запускаем процесс
                                if ($search_child)
                                {
                                    $arrSearchPath[] = $search_path;
                                    $ar["CHILDREN"] = GetTreeRecursive($ROOT, $ar["SEARCH_PATH"], $level+1, $fake_level+1, $max_depth, $get_description);
                                }

                                // Вот сюда вклиниваемся со своим $parent
                                if ( !isset($parent) || $ar['LEVEL'] == $parent['LEVEL'] )
                                {
                                    $map[] 	= $ar;
                                    $parent =& $map[(count($map) -1)];
                                }
                                elseif ( $ar['LEVEL'] > $parent['LEVEL'] )
                                {
                                    $parent['CHILDREN'][] = $ar;
                                }
                            }
                        }
                    }
                }
            }
        }

        if ((! count($map))&&($level<$max_depth))
        {
            $map=GetTreeRecursive($ROOT, $PARENT_PATH, $level+1, $fake_level, $max_depth, $get_description);
        }

        return $map;
    }
}

if (!function_exists('CreateMapStructure'))
{
    function CreateMapStructure($arMap)
    {
        $arReturn = array();

        foreach ($arMap as $key => $arMapItem)
        {
            $arChildrenItems = $arMapItem["CHILDREN"];
            unset($arMapItem["CHILDREN"]);

            $arMapItem["STRUCT_KEY"] = $key;

            $arReturn[] = $arMapItem;
            if (is_array($arChildrenItems) && count($arChildrenItems) > 0)
            {
                $arChildren = CreateMapStructure($arChildrenItems);
                $arReturn = array_merge($arReturn, $arChildren);
            }
        }

        return $arReturn;
    }
}

$additionalCacheID = $USER->GetGroups();

if ($this->StartResultCache(false, $additionalCacheID))
{

    $sl = CLang::GetList($dummy1="", $dummy2="");
    $lang = $arParams['FORCE_LANG']?$arParams['FORCE_LANG']:LANG;
    while ($slr = $sl->Fetch())
    {
        if ($slr["LID"] == $lang)
        {
            $lang_dir = $slr['DOC_ROOT'].$slr["DIR"];
            break;
        }
    }
    $arResult["arMapStruct"] = GetTree($lang_dir, $arParams["LEVEL"], $arParams["SHOW_DESCRIPTION"] == "Y");

    $arResult["arMap"] = CreateMapStructure($arResult["arMapStruct"]);

    $this->IncludeComponentTemplate();
}
?>