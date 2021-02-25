<?php

namespace drcms5\addon\util;
class AddonUrl
{

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

    static public function __callStatic($name, $arguments)
    {
        $thinkVer = str_replace('.', '', DrTool::ThinkVer());
        $class    = "drcms5\\addon\\think{$thinkVer}\\util\\UrlSDK";
        return call_user_func_array("{$class}::{$name}", $arguments);
    }
}