<?
	global $MESS;
	$strPath2Lang = str_replace("\\", "/", __FILE__);
	$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
	include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

	Class maycat_routecontrol extends CModule
	{
		var $MODULE_ID = 'maycat.routecontrol';
		var $MODULE_VERSION;
		var $MODULE_VERSION_DATE;
		var $MODULE_NAME;
		var $MODULE_FOLDER;
		var $MODULE_DESCRIPTION;
		var $MODULE_CSS;
		var $MODULE_GROUP_RIGHTS = "Y";

		const BITRIX_HOLDER = "bitrix";
		const LOCAL_HOLDER = "local";
		var $MODULE_HOLDER = '';

		function __construct()
		{
			$arModuleVersion = array();

			$path = str_replace("\\", "/", __FILE__);
			$this->MODULE_FOLDER = substr($path, 0, strlen($path)-strlen("/install/index.php"));
			$path = substr($path, 0, strlen($path) - strlen("/index.php"));

			include($path."/version.php");

            // Подгружаем локализованные названия
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->MODULE_NAME = GetMessage("SCOM_INSTALL_NAME");
			$this->MODULE_DESCRIPTION = GetMessage("SCOM_INSTALL_DESCRIPTION");
			$this->PARTNER_NAME = GetMessage("SPER_PARTNER");
			$this->PARTNER_URI = GetMessage("PARTNER_URI");

			$moduleHolder = self::LOCAL_HOLDER;
			$pathToInclude = $_SERVER['DOCUMENT_ROOT']."/".$moduleHolder."/modules/".$this->MODULE_ID."/include.php";
			if (!file_exists($pathToInclude))
				$moduleHolder = self::BITRIX_HOLDER;

			$this->MODULE_HOLDER = $moduleHolder;
		}

        function DoInstall()
        {
            global $APPLICATION, $step;
            CopyDirFiles($_SERVER['DOCUMENT_ROOT']."/".$this->MODULE_HOLDER."/modules/".$this->MODULE_ID."/install/admin", $_SERVER['DOCUMENT_ROOT']."/bitrix/admin");
            $this->InstallDB(false);
            $this->InstallEvents();
            CopyDirFiles(
                $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components",
                $_SERVER["DOCUMENT_ROOT"]."/bitrix/components",
                true, true);
            // @todo: Нужно проверять существующие меню и смотреть, нет ли там в адресах константы SITE_ID и/или вызовы функций.
            // Проходиться надо по всем сайтам и список кривых меню выдавать юзеру, мол, отследи.
            return true;
        }

        function DoUninstall()
        {
            global $APPLICATION, $step;
            $this->UnInstallDB();
            DeleteDirFiles($_SERVER['DOCUMENT_ROOT']."/".$this->MODULE_HOLDER."/modules/".$this->MODULE_ID."/install/admin", $_SERVER['DOCUMENT_ROOT']."/bitrix/admin");
            $this->UnInstallEvents();
            DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/routecontrol/");
            return true;
        }

		function InstallDB($install_wizard = true)
		{
			global $DB, $DBType, $DBName, $APPLICATION;
			$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/".$this->MODULE_HOLDER."/modules/".$this->MODULE_ID."/install/db/install.sql");

			if($this->errors !== false)
			{
				$APPLICATION->ThrowException(implode("", $this->errors));
				return false;
			}

            RegisterModule($this->MODULE_ID);
			return true;
		}

		function UnInstallDB($arParams = Array())
		{
			global $DB, $DBType, $APPLICATION;
			$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT']."/".$this->MODULE_HOLDER."/modules/".$this->MODULE_ID."/install/db/uninstall.sql");

			if($this->errors !== false)
			{
				$APPLICATION->ThrowException(implode("", $this->errors));
				return false;
			}

			#menu
			UnRegisterModule($this->MODULE_ID);
			return true;
		}


	}
?>