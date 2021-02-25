<?php

namespace drcms5\addon;

use drcms5\addon\util\Config;
use drcms5\addon\util\DrTool;
use think\View;

abstract class Addon
{
    private static $addon_path = null;
    private static $addon_info = [];
    private static $instance   = null;
    protected      $error;
    private static $config     = [];
    protected      $view       = null;

    // 尽量不要在构造方法中写过多的逻辑代码
    public function __construct()
    {
        $view_path = $this->addon_path() . DIRECTORY_SEPARATOR;
        $thinkVer  = floatval(DrTool::ThinkVer());
        if ($thinkVer >= 5.1) {
            $template_conf              = Config::get('template');
            $template_conf['view_path'] = $view_path;
            $this->view                 = think\Container::get('view')->engine($template_conf);
        } else if ($thinkVer >= 6.0) {
            // 待续
        } else {
            $template_conf              = Config::get('template');
            $template_conf['view_path'] = $view_path;
            $this->view                 = new View($template_conf, Config::get('view_replace_str'));
        }
        // draddon是配置域
        Config::set('draddon.' . $this->getName(), [
            'config' => $this->getConfig(),
            'info'   => $this->getInfo()
        ]);
        // 控制器初始化
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
    }

    public static function instance(...$arg)
    {
        if (self::$instance) {
            self::$instance = new static(...$arg);
        }
        return self::$instance;
    }

    final public function is_intact()
    {
        $addon_path = $this->addon_path();
        if (!is_dir($addon_path)) {
            throw new AddonException('插件目录不存在');
        }
        $info_field = ['name', 'title', 'intro', 'author', 'version', 'status'];
        $addon_info = $this->getInfo();
        $is_intact  = true;
        if (array_intersect($info_field, array_keys($addon_info)) != $info_field) {
            $is_intact = false;
        }/* else if (is_file($this->addon_path() . DIRECTORY_SEPARATOR . 'config.php')) {
            $is_intact = false;
        }*/
        if (!$is_intact) throw new AddonException('插件信息不全' . static::class);
        return $is_intact;
    }

    final public function getConfig($key = '')
    {
        $config_file = $this->addon_path() . DIRECTORY_SEPARATOR . 'config.php';
        if (!self::$config && is_file($config_file)) {
            self::$config = include $config_file;
        }
        if ($key && isset(self::$config[$key]))
            return self::$config[$key];
        return self::$config ? self::$config : [];
    }

    final  public function getName()
    {
        $data = explode('\\', static::class);
        return strtolower(array_pop($data));
    }

    final public function getInfo()
    {
        if (self::$addon_info && is_array(self::$addon_info)) {
            return self::$addon_info;
        }
        $dir  = $this->addon_path();
        $file = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'info.php';
        if (!is_file($file)) throw  new AddonException('插件信息缺失：' . $file);
        self::$addon_info                = include $file;
        self::$addon_info['update_time'] = filemtime($file);
        return self::$addon_info;
    }

    final public function addon_path()
    {
        if (self::$addon_path) return self::$addon_path;
        $class     = static::class;
        $namespace = Config::get('draddon.addon_namespace');
        $baseDir   = Config::get('draddon.addon_path');
        if (!strstr($class, $namespace . '\\')) {
            throw new AddonException(sprintf('非法的插件命名空间 %s', $class));
        }
        $suffix = str_replace($namespace, '', $class);
        if (!defined('EXT')) {
            define('EXT', '.php');
        }
        self::$addon_path = str_replace('\\', DIRECTORY_SEPARATOR, $suffix);
        self::$addon_path = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim(self::$addon_path, DIRECTORY_SEPARATOR) . EXT;
        self::$addon_path = dirname(self::$addon_path);
        return self::$addon_path;
        throw new AddonException(sprintf('非法的插件路径 %s', $class));
    }

    public function setStatus($status)
    {
        if (!in_array($status, [0, 1])) {
            return true;
        }
        $path           = $this->addon_path();
        $info           = $this->getInfo();
        $info['status'] = $status;
        return $this->setInfo($info);
    }

    public function setInfo($info)
    {
        $file = rtrim($this->addon_path(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'info.php';
        return file_put_contents($file, '<?php return ' . var_export($info, true) . ';');
    }

    public function fetch($template)
    {
        if (!is_file($template)) $template = '/' . $template;
        return $this->view->fetch($template);
    }

    public function display($template)
    {
        echo $this->fetch($template);
    }


    public function getError()
    {
        return $this->error;
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();

    public function enable()
    {
    }

    public function disable()
    {
    }
}