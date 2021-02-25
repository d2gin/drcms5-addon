<?php

namespace drcms5\addon;

use drcms5\addon\util\Config;
use drcms5\addon\util\DrTool;

class Service
{
    const FILES_CONFLICT = -3;
    static private $addons = [];

    public static function list($status = '')
    {
        $list    = [];
        $path    = Config::get('draddon.addon_path');
        $results = scandir($path);
        foreach ($results as $name) {
            if ($name === '.' or $name === '..')
                continue;
            if (is_file($path . $name))
                continue;
            $addon_path = $path . $name . DIRECTORY_SEPARATOR;
            if (!is_dir($addon_path))
                continue;
            if (!is_file($addon_path . DrTool::parseName($name, 1) . '.php'))
                continue;
            $info                                 = self::get_addon_instance($name)->getInfo();
            $list[intval($info['status'])][$name] = $info;
        }
        krsort($list);
        if (in_array($status, ['0', '1'])) {
            return $list[$status];
        }
        return $list;
    }

    public static function get_addon_instance($name): Addon
    {
        if (!$name) {
            throw new AddonException('请填入插件名');
        } else if (!isset(self::$addons[$name]) || self::$addons[$name] instanceof Addon) {
            $class = Config::get('draddon.addon_namespace') . "\\{$name}\\" . DrTool::parseName($name, 1);
            if (!class_exists($class)) {
                throw new AddonException('插件启动类不存在');
            }
            self::$addons[$name] = new $class();
        }
        return self::$addons[$name];
    }

    public static function get_addon_class($name, $type = 'boot')
    {
        $class = '';
        if ($type == 'boot') {
            $class = Config::get('draddon.addon_namespace') . "\\{$name}\\" . DrTool::parseName($name, 1);
        } else {
            // 兼容多级类目 兼容控制器.号分割
            $ex        = array_filter(explode('/', implode('/', explode('.', $name))));
            $name      = array_pop($ex);
            $basespace = implode('\\', $ex);
            $class     = Config::get('draddon.addon_namespace') . "\\{$basespace}\\{$type}\\" . DrTool::parseName($name, 1);
        }
        return $class;
    }

    public static function get_hook_list($name)
    {
        $methods = get_class_methods(self::get_addon_class($name));
        $list    = [];
        $sys     = get_class_methods(Addon::class);
        $diff    = array_diff($methods, $sys);
        return $diff;
    }

    /**
     * 获取插件在全局的文件
     *
     * @param string $name 插件名称
     * @return  array
     */
    public static function get_mainapp_files($name, $only_conflict = false)
    {
        $list      = [];
        $path      = Config::get('draddon.addon_path');
        $addon_dir = $path . $name . DIRECTORY_SEPARATOR;
        // 扫描插件目录是否有覆盖的文件
        foreach (self::get_mainapp_dir() as $dir => $dst) {
            $mainapp_dir = DrTool::rootpath() . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
            if (!is_dir($mainapp_dir))
                continue;
            //检测到存在插件外目录
            if (is_dir($addon_dir . $dir)) {
                //匹配出所有的文件
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($addon_dir . $dir, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($files as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        $filePath = $fileinfo->getPathName();
                        if ($only_conflict) {
                            $destPath = preg_replace('!^' . preg_quote($addon_dir . $dir . DIRECTORY_SEPARATOR) . '!', $dst, $filePath);
                            if (is_file($destPath)) {
                                if (filesize($filePath) != filesize($destPath) || md5_file($filePath) != md5_file($destPath)) {
                                    $list[] = $destPath;
                                }
                            }
                        } else {
                            $destPath = preg_replace('!^' . preg_quote($addon_dir . DIRECTORY_SEPARATOR . $dir) . '!', $dst, $filePath);
                            $list[]   = $destPath;
                        }
                    }
                }
            }
        }
        return $list;
    }

    public static function is_conflict($name)
    {
        if ($list = self::get_mainapp_files($name, true)) {
            return $list;
        }
        return false;
    }

    public static function get_mainapp_dir()
    {
        return Config::get('draddon.mainapp_dir');
    }

    public static function hook_list($name)
    {
    }

    public static function copy_dir($src, $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                $sontDir = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                if (!is_dir($sontDir)) {
                    mkdir($sontDir, 0755, true);
                }
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    public static function rmdir($dirname, $withself = true)
    {
        if (!is_dir($dirname))
            return false;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirname, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            if ($fileinfo->isDir()) {
                @rmdir($fileinfo->getRealPath());
            } else {
                @unlink($fileinfo->getRealPath());
            }
        }
        if ($withself) @rmdir($dirname);
        return true;
    }

    /**
     * 获取插件源资源文件夹
     * @param string $name 插件名称
     * @return  string
     */
    protected static function get_source_assets_dir($name)
    {
        return Config::get('draddon.addon_path') . $name . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
    }

    /**
     * 获取插件目标资源文件夹
     * @param string $name 插件名称
     * @return  string
     */
    protected static function get_dest_assets_dir($name)
    {
        $assetsDir = DrTool::rootpath() . str_replace("/", DIRECTORY_SEPARATOR, "public/assets/addons/{$name}/");
        if (!is_dir($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }
        return $assetsDir;
    }

    /**
     * @param $name
     */
    public static function install($name, $force = false)
    {
        $addon = self::get_addon_instance($name);
        if (!$addon) throw new AddonException('插件不存在');
        // 插件信息是否完整
        $is_full = $addon->is_intact();
        if (!$force) {
            $is_conflict = self::is_conflict($name);
            if ($is_conflict) throw new AddonException('发现冲突文件', self::FILES_CONFLICT, $is_conflict);
        }
        $addon_dir  = Config::get('draddon.addon_path');
        $src_assets = self::get_source_assets_dir($name);
        // 移动插件资源目录
        if (is_dir($src_assets)) {
            $dest_assets = self::get_dest_assets_dir($name);
            self::copy_dir($src_assets, $dest_assets);
        }
        $mainapp_dirs = self::get_mainapp_dir();
        var_dump($mainapp_dirs);
        foreach ($mainapp_dirs as $src => $dest) {
            $srcDir = $addon_dir . $name . DIRECTORY_SEPARATOR . $src;
            if (is_dir($srcDir)) {
                self::copy_dir($srcDir, $dest);
            }
        }
        $addon->install();
    }

    public static function uninstall($name, $force = false)
    {
        $addon = self::get_addon_instance($name);
        if (!$addon) throw new AddonException('插件不存在');
        if (!$force) {
            $is_conflict = self::is_conflict($name);
            if ($is_conflict) throw new AddonException('发现冲突文件', self::FILES_CONFLICT, $list);
        }
        $mainapp_files = self::get_mainapp_files($name);
        $dest_assets   = self::get_dest_assets_dir($name);
        if (is_dir($dest_assets)) {
            // 删除资源目录
            self::rmdir($dest_assets);
        }
        if ($force) {
            foreach ($mainapp_files as $file) {
                @unlink(DrTool::rootpath() . $file);
            }
        }
        // 移除插件目录
        self::rmdir(Config::get('draddon.addon_path') . $name);
        $addon->uninstall();
    }

    public static function enable($name)
    {
        $addon = self::get_addon_instance($name);
        if (!$addon) throw new AddonException('插件不存在');
        $addon->enable();
        $addon->setStatus(1);
    }

    public static function disable($name)
    {
        $addon = self::get_addon_instance($name);
        if (!$addon) throw new AddonException('插件不存在');
        $addon->disable();
        $addon->setStatus(0);
    }
}