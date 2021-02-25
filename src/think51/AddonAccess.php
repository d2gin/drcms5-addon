<?php

namespace drcms5\addon\think51;

use drcms5\addon\util\AddonUrl;
use drcms5\addon\Service;
use drcms5\addon\util\Config;
use think\facade\App;
use think\facade\Hook;
use think\facade\Request;
use think\facade\Response;
use think\exception\HttpResponseException;
use think\Loader;

class AddonAccess
{
    public function run()
    {
        $route_info = Request::routeInfo();
        $route_vars = $route_info['var'];

        $addon_module_var     = AddonUrl::get_module_var();
        $addon_controller_var = AddonUrl::get_controller_var();
        $addon_action_var     = AddonUrl::get_action_var();

        $addon_name       = $route_vars[$addon_module_var];
        $addon_controller = (isset($route_vars[$addon_controller_var]) && $route_vars[$addon_controller_var]) ? $route_vars[$addon_controller_var] : Config::get('default_controller');
        $addon_controller = Loader::parseName($addon_controller, 1);
        $addon_action     = (isset($route_vars[$addon_action_var]) && $route_vars[$addon_action_var]) ? $route_vars[$addon_action_var] : Config::get('default_action');

        // 监听addon_begin
        $dispatch = Request::dispatch();
        Hook::listen('addon_begin', $dispatch);
        try {
            // 取得控制器
            $class = Service::get_addon_class($addon_name . '/' . $addon_controller, 'controller');
            $data  = App::invokeMethod([$class, $addon_action], $route_vars);
        } catch (HttpResponseException $exception) {
            $data = $exception->getResponse();
        }

        // 输出数据到客户端
        if ($data instanceof Response) {
            $response = $data;
        } elseif (!is_null($data)) {
            // 默认自动识别响应输出类型
            $isAjax   = Request::isAjax();
            $type     = $isAjax ? Config::get('default_ajax_return') : Config::get('default_return_type');
            $response = Response::create($data, $type);
        } else {
            $response = Response::create();
        }

        // 监听app_end
        Hook::listen('app_end', $response);

        return $response->send();
    }
}