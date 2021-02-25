<?php

namespace drcms5\addon\util;
class Config
{
    static public function __callStatic($name, $arguments)
    {
        if (DrTool::ThinkVer() == 5.0 && $name == 'get' && substr($arguments[0], -1, 1) == '.') {
            $arguments[0] = substr($arguments[0], 0, -1);
        }
        $thinkVer = str_replace('.', '', DrTool::ThinkVer());
        $class    = "drcms5\\addon\\think{$thinkVer}\\util\\Config";
        return call_user_func_array("{$class}::{$name}", $arguments);
    }
}