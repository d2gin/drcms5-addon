<?php

namespace drcms5\addon\util;
class DrTool
{
    public static function ThinkVer()
    {
        $version = 0;
        // thinkphp 5.1 6.0
        if (class_exists('think\App') && defined('think\App::VERSION')) {
            $version = \think\App::VERSION;
            if (strpos($version, '5.1') === 0) {
                // 5.1 支持
                return '5.1';
            } else if (strpos($version, '6.0') === 0) {
                // 6.0 支持
                return '6.0';
            }
        }
        // thinkphp3.2 - 5.0
        if (defined('THINK_VERSION')) {
            $version = THINK_VERSION;
            if (strpos($version, '5.0') === 0) {
                return '5.0';
            }
            // 不支持3.2及以下版本
            return 0;
        }
        return $version;
    }

    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string $name 字符串
     * @param integer $type 转换类型
     * @param bool $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }

    public static function is_cli()
    {
        return PHP_SAPI == 'cli';
    }

    public static function rootpath()
    {
        if (defined('ROOT_PATH') && floatval(self::ThinkVer()) < 5.1) {
            return ROOT_PATH;
        } else if (class_exists('think\Container') && class_exists('think\App') && method_exists('think\App', 'getRootPath')) {
            return \think\Container::get('app')->getRootPath();
        }
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . '../../../../../');
    }
    public static function apppath()
    {
        if (defined('APP_PATH') && floatval(self::ThinkVer()) < 5.1) {
            return APP_PATH;
        } else if (class_exists('think\Container') && class_exists('think\App') && method_exists('think\App', 'getRootPath')) {
            return \think\Container::get('app')->getAppPath();
        }
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . '../../../../../application/');
    }
}