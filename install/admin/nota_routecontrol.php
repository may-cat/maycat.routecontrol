<?
    // "bitrix" folder or "local" folder - that is the question
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."/local/modules/maycat.routecontrol/admin/maycat_routecontrol.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/maycat.routecontrol/admin/maycat_routecontrol.php");
	elseif(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/maycat.routecontrol/admin/maycat_routecontrol.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/maycat.routecontrol/admin/maycat_routecontrol.php");
?>