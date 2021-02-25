<?php

namespace drcms5\addon;

use drcms5\addon\util\AddonUrl;
use drcms5\addon\util\Config;
use drcms5\addon\util\DrTool;
use think\View;

class Controller extends \think\Controller
{
    public function __construct()
    {
        if (floatval(DrTool::ThinkVer()) >= 6) {
            throw  new AddonException('thinkphp6以上版本不再需要继承 drcms5\addon\Controller');
        }
        $request                    = $this->request;
        $route_info                 = $request->routeInfo();
        $route_vars                 = $route_info['var'];
        $template_conf              = Config::get('template');
        $addon_module_var           = AddonUrl::get_module_var();
        $addon_name                 = $route_vars[$addon_module_var];
        $basepath                   = Config::get('draddon.addon_path') . $addon_name;
        $template_conf['view_path'] = $basepath . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
        if (floatval(DrTool::ThinkVer()) == 5.1) {
            $this->view = $this->app->view->init($template_conf);
        } else {
            $this->view = View::instance($template_conf, Config::get('view_replace_str'));
        }
        parent::__construct($request);
    }

    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $route_info           = $this->request->routeInfo();
        $addon_controller_var = AddonUrl::get_controller_var();
        $addon_action_var     = AddonUrl::get_action_var();
        $route_vars           = $route_info['var'];
        $addon_controller     = $route_vars[$addon_controller_var];
        $addon_action         = $route_vars[$addon_action_var];
        if ($template == '') {
            $template = '/' . $addon_controller . '/' . $addon_action;
        } else if (0 !== strpos($template, '/')) {
            $template = '/' . $addon_controller . '/' . $template;
        }
        return parent::fetch($template, $vars, $replace, $config);
    }

    protected function display($content = '', $vars = [], $replace = [], $config = [])
    {
        return parent::display($content, $vars, $replace, $config);
    }
}