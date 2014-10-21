<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CBitrixComponent::includeComponentClass("bitrix:menu");

class CRoutecontrolMenuComponent extends CBitrixMenuComponent
{
    public function getChildMenuRecursive(&$arMenu, &$arResult, $menuType, $use_ext, $menuTemplate, $currentLevel, $maxLevel, $bMultiSelect, $bCheckSelected)
    {
        CModule::IncludeModule('maycat.routecontrol'); // подключить модуль maycat.routecontrol, без него не будет работать!
        foreach ($arMenu as $k=>$arMenuitem)
        {
            if (! \Maycat\Routecontrol\RCMenuLockTable::isValidUrl($arMenuitem['LINK'],SITE_ID))
                array_splice($arMenu,$k,1);
        }

        parent::getChildMenuRecursive($arMenu, $arResult, $menuType, $use_ext, $menuTemplate, $currentLevel, $maxLevel, $bMultiSelect, $bCheckSelected);
    }

    public function executeComponent()
    {
        // Изврат, чтобы подцепить component.php оригинального компонента.
        // По идее это должен делать сам Битрикс, но он этого не делает.
        /** @noinspection PhpUnusedLocalVariableInspection */
        global $APPLICATION, $USER, $DB;


        //these vars are used in the component file
        $arParams = &$this->arParams;
        $arResult = &$this->arResult;

        $componentPath = $this->__path;
        $componentName = $this->__name;
        $componentTemplate = $this->getTemplateName();

        if ($this->__parent)
        {
            $parentComponentName = $this->__parent->__name;
            $parentComponentPath = $this->__parent->__path;
            $parentComponentTemplate = $this->__parent->getTemplateName();
        }
        else
        {
            $parentComponentName = "";
            $parentComponentPath = "";
            $parentComponentTemplate = "";
        }

        include $_SERVER["DOCUMENT_ROOT"].'/bitrix/components/bitrix/menu/component.php';
    }


    // @todo: Сделать, чтобы за шаблонами ходил к компоненту bitrix:menu! Или, может, решить проблему симлинками?
}
