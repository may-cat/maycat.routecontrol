<?
namespace Maycat\Routecontrol;

/**
 * Class CMenuLockUrlsTable
 * @package Maycat\Routecontrol
 */
class RCAccessLockTable extends RCCommonTable
{
    public static function getFilePath()
    {
        return __FILE__;
    }

    public static function getTableName()
    {
        return 'n_routecontrol_accesslock';
    }
}