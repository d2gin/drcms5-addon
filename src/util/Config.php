<?php

namespace drcms5\addon\util;
class Config
{
    static public function __callStatic($name, $arguments)
    {
        $thinkVer = str_replace('.', '', DrTool::ThinkVer());
        $class    = "drcms5\\addon\\think{$thinkVer}\\util\\Config";
        return call_user_func_array("{$class}::{$name}", $arguments);
    }
}