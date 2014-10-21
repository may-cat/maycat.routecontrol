routecontrol, 0.1
======

Модуль, который позволяет полностью контролировать меню и структуру сайта.

Классические компоненты Битрикса не очень хорошо работают с большой вложенностью меню и
не позволяют получить полноценную карту сайта в php-коде сайта.
Данный модуль - позволяет.

## Что нужно сделать:

 - установить модуль
 - заменить вызовы bitrix:menu на routecontrol:menu, и, если есть шаблоны меню, их нужно переместить, чтобы они подхватывались
компонентом routecontrol:menu (или поставить симлинки)
 - зайти в админку (смотри usage.jpg) и управлять видимостью разделов меню из админки.

## Зачем это?!

 - Это может быть нужно для типовых решений и/или ситуаций, когда модераторам сайта нельзя давать доступа к управлению файлами
 - Как, вы не поняли?! У вас есть возможность нормально получить дерево сайта, ведь модуль это как-то делает!

## Технические фишки

1. Компонент routecontrol:menu не дублирует функционал стандартного меню, а именно расширяет его. Апдейты Битрикса
 не страшны!
2. Основная магия концентрируется в routecontrol:sitemap, который в отличие от стандартного компонента может
ходить по меню бесконечной вложенности.
Благодаря workaround'у (см. шаблон array_export) компонент может вернуть структуру сайта не в виде html-кода, а в виде
массива. Благодаря чему Вы можете, например, отдавать её по API какому-нибудь стороннему сайту.

------

Known Bugs:
1. Нужно вынести структуру меню в настройки модуля
2. Если на сайте в меню используется константа SITE_ID - это создаёт большие трудности =(