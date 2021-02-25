<?php

namespace drcms5\addon\think50\util;

use drcms5\addon\util\DrTool;

class Config
{

    static public function __callStatic($name, $arguments)
    {
        return call_user_func_array("think\\Config::{$name}", $arguments);
    }
}