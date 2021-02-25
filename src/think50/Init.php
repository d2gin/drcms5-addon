<?php

namespace drcms5\addon\think50;

use drcms5\addon\Service;
use drcms5\addon\util\AddonUrl;
use drcms5\addon\util\DrTool;
use think\Route;
use think\Config;
use think\Hook;
use think\Console;
use think\Loader;

class Init
{
    static public function drun()
    {
        // 插件初始化
        Config::load(__DIR__ . DIRECTORY_SEPARATOR . '../config/config.php', 'draddon');
        Loader::addNamespace(Config::get('draddon.addon_namespace'), Config::get('draddon.addon_path'));
        // 插件机制
        $addons = Service::list(1);
        foreach ($addons as $addon) {
            $hooks = Service::get_hook_list($addon['name']);
            array_map(function ($v) use ($addon) {
                $addon_class = Service::get_addon_class($addon['name']);
                Hook::add($v, [$addon_class, $v]);
            }, $hooks);
        }
        
        if (DrTool::is_cli()) {
            // 注册指令
            $console = Console::init(false);
            $console->add(new Cmd());
        }
        // 注册插件访问
        $addon_module_var     = AddonUrl::get_module_var();
        $addon_controller_var = AddonUrl::get_controller_var();
        $addon_action_var     = AddonUrl::get_action_var();
        Route::any(['draddon', "draddon/:{$addon_module_var}/[:{$addon_controller_var}]/[:{$addon_action_var}]"], 'drcms5\addon\think50\AddonAccess@run');
        // hook
        Hook::add('app_begin', function () {
            // 加载助手函数
            $helper = __DIR__ . DIRECTORY_SEPARATOR . "../helper.php";
            if (is_file($helper)) include $helper;
        }, true);
    }
}