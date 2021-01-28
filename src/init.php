<?php

use think\Route;
use think\Config;
use think\Hook;
use think\Console;
use think\Loader;
use drcms5\addon\Service;
use drcms5\addon\Cmd;
use drcms5\addon\util\AddonUrl;

// 插件初始化
Config::load(__DIR__ . DS . 'config/config.php', 'draddon');
Loader::addNamespace(Config::get('draddon.addon_namespace'), Config::get('draddon.addon_path'));
{
    // 注册指令
    $console = Console::init(false);
    $console->add(new Cmd());
}
{
    // 注册插件访问
    $addon_module_var     = AddonUrl::get_module_var();
    $addon_controller_var = AddonUrl::get_controller_var();
    $addon_action_var     = AddonUrl::get_action_var();

    Route::any(['draddon', "draddon/:{$addon_module_var}/[:{$addon_controller_var}]/[:{$addon_action_var}]"], 'drcms5\addon\AddonAccess@run');
}
{
    // 插件机制
    $addons = Service::list(1);
    foreach ($addons as $addon) {
        $hooks = Service::get_hook_list($addon['name']);
        array_map(function ($v) use ($addon) {
            $addon_class = Service::get_addon_class($addon['name']);
            Hook::add($v, [$addon_class, $v]);
        }, $hooks);
    }
}