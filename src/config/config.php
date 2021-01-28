<?php
return [
    'addon_namespace'      => 'addons',
    'addon_path'           => ROOT_PATH . 'addons' . DS,
    'addon_module_var'     => '__addon_name',
    'addon_controller_var' => '__addon_controller',
    'addon_action_var'     => '__addon_action',
    'mainapp_dir'          => [
        // 源目录 => 目标目录
        'application' => APP_PATH,
        'public'      => ROOT_PATH . DS . 'public'
    ]
];