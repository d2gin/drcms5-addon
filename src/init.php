<?php

use drcms5\addon\util\DrTool;
$ver = str_replace('.', '', DrTool::ThinkVer());
if ($ver) call_user_func("drcms5\\addon\\think{$ver}\\Init::drun");