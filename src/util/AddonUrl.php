<?php

namespace drcms5\addon\util;

use think\Config;
use think\Request;
use think\Route;
use think\Url;

/**
 * Class AddonUrl
 * @package drcms5\addon\util
 */
class AddonUrl extends Url
{
    /**
     * 生成插件访问地址
     * 不要试图用它生成主程序的URL
     * @param string $url
     * @param string $var
     * @param bool $suffix
     * @param bool $domain
     * @return string
     */
    public static function build($url = '', $var = '', $suffix = true, $domain = false)
    {
        $url = trim($url, '/');
        if (!preg_match('/^draddon\//i', $url)) {
            $url = 'draddon/' . $url;
        }
        $ex = array_filter(explode('/', $url));
        if (count($ex) < 3) {
            array_shift($ex);
        } else if (count($ex) < 2) {
            return parent::build($url, $var, $suffix, $domain);
        }
        $arg = [
            self::get_module_var()     => array_shift($ex),
            self::get_controller_var() => array_shift($ex),
            self::get_action_var()     => array_shift($ex),
        ];
        if (is_string($var)) parse_str($var, $var);
        $var = array_merge($var, $arg);
        return parent::build('[draddon]', $var, $suffix, $domain);
    }

    public static function get_module_var()
    {
        $addon_module_var = str_replace([':', '[', ']'], '', Config::get('draddon.addon_module_var'));
        return $addon_module_var ? $addon_module_var : '__addon_name';
    }

    public static function get_controller_var()
    {
        $addon_controller_var = str_replace([':', '[', ']'], '', Config::get('draddon.addon_controller_var'));
        return $addon_controller_var ? $addon_controller_var : '__addon_controller';
    }

    public static function get_action_var()
    {
        $addon_action_var = str_replace([':', '[', ']'], '', Config::get('draddon.addon_action_var'));
        return $addon_action_var ? $addon_action_var : '__addon_action';

    }
}