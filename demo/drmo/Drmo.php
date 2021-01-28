<?php
namespace addons\drmo;
use drcms5\addon\Addon;

class Drmo extends Addon {
    public function login_input_concat() {
        $this->display('/vcode');
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