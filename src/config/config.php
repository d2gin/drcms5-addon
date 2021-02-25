<?php
return [
    'addon_namespace'      => 'addons',
    'addon_path'           => \drcms5\addon\util\DrTool::rootpath() . 'addons' . DIRECTORY_SEPARATOR,
    'addon_module_var'     => '__addon_name',
    'addon_controller_var' => '__addon_controller',
    'addon_action_var'     => '__addon_action',
    'mainapp_dir'          => [
        // 源目录 => 目标目录
        'application' => \drcms5\addon\util\DrTool::apppath(),
        'public'      => \drcms5\addon\util\DrTool::rootpath() . DIRECTORY_SEPARATOR . 'public'
    ]
];