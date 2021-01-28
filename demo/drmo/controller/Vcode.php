<?php
namespace addons\drmo\controller;
use drcms5\addon\Controller;
use drcms5\addon\util\AddonUrl;

class Vcode extends Controller {
    public function image() {
        return $this->fetch();
    }
}