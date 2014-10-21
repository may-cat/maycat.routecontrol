<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$GLOBALS[$arParams['VAR_RESULT']]=array('from resmod');

if (! function_exists('MaycatTreeWalker'))
{
    global $maycatTreeId;
    $maycatTreeId=0;
    function MaycatTreeWalker($ar,$arParentItem)
    {
        global $maycatTreeId;
        $arAll=array();
        foreach ($ar as $key=>$v)
        {
            $maycatTreeId++;

            $class = null;
            if ($v['PARAMS']['ASUP'])
                $class = $v['PARAMS']['ASUP'];
            elseif ($v['DIR_PROPS']['ASUP'])
                $class = $v['PARAMS']['ASUP'];
            elseif ($arParentItem['class'] && !isset($arParentItem['no_child']))
                $class = $arParentItem['class'];

            $arItem = array(
                'id' => $maycatTreeId,
				'hash_id' => md5($v['FULL_PATH']),
                'parent' => $arParentItem['id'],
                'class' => $class,
                'name' => $v['NAME'],
                'path' => $v['FULL_PATH'],
				'no_child' => $v['PARAMS']['no_child'],
				'from_iblock' => $v['PARAMS']['FROM_IBLOCK'],
                //'v' => $v
            );
			
			if ( isset( $v['PARAMS']['section_code'] ) )
				$arItem['section_code'] = $v['PARAMS']['section_code'];
			
            $children = MaycatTreeWalker($v['CHILDREN'],$arItem);
            if (count($children))
                $arItem['children'] = $children;

			if ( !$arItem['from_iblock'] && isset($arItem['class']) && !isset($arItem['section_code']) || $v['PARAMS']['HIDE_CLASS'] )
				$arItem['class'] = null;
			
            $arAll[]=$arItem;
        }
        return $arAll;
    }
}
if( $_REQUEST['debug'] == 'y' )
{
	echo '<pre>';
		print_r($arResult['arMapStruct']);
	echo '</pre>'; die();
}
$arAll=MaycatTreeWalker($arResult['arMapStruct'],array('id'=>0));

$cp = $this->__component; // объект компонента
$cp->arResult = $arAll;;