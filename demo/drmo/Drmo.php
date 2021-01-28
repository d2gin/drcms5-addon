<?php

namespace addons\drmo;

use drcms5\addon\Addon;

class Drmo extends Addon
{
    // 这是一个hook方法 监听hook请不要和Addon类的方法相冲突
    public function login_input_concat()
    {
        // 这是实现一个登陆验证码功能插件
        $this->display('/vcode');
    }

    public function app_begin()
    {
        echo nl2br("drmo 插件示例。我是 app_begin hook， 我拦截了程序。\n");
    }

    public function install()
    {
        // TODO: Implement install() method.
    }

    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }
}