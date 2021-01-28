# drcms5-addon
thinkphp5.0 实现插件功能

### 项目介绍

这是一个基于 thinkphp5.0实现的插件依赖，目前只支持5.0版本，切勿在5.1及以上使用。

### 环境要求

php >= 7.0

thinkphp 5.0

composer

### 安装

```
composer require icy8/drcms5-addon
```

### 功能介绍

1. **安装机制：**插件的安装目前没有接入数据库，只读取插件目录。设计上有两个目录是主程序覆盖目录，分别是`application`、`public`，这两个目录会在安装时移动覆盖主程序的文件/目录，有冲突会中断安装。还有一个目录是资源目录`assets`，安装过程中会在程序根目录自动创建`[ROOT_PATH]/public/assets/addons/[addo_name]/` 并将文件移入该目录。
2. **卸载机制：**删除根目录的资源文件；删除插件目录。
3. **开启机制：**暂无
4. **关闭机制：**暂无
5. 上述机制均可在插件主类中自定义`install`、`uninstall`、`enable`、`disable` 方法进行扩展。
6. **插件主类：**类文件必须放置在 `addons/[addon_name]/[addon_name].php` 类名必须与插件名一致。
7. **插件访问：**http://127.0.0.1/addon/[addon_name]/[controller]/[action] 过程会自动调用**插件控制器**。
8. **插件控制器：**插件访问的基础，所有控制器都必须放置在`addons/[addon_name]/controller` 文件夹中，支持多级控制器。插件控制器必须继承`drcms5\addon\Controller`，否则会导致模版引用失败。
9. **插件模版：**同上，文件放在`addons/[addon_name]/view`。
10. **URL生成：**用于插件访问的URL可以用函数`addon_url($url = '', $vars = '', $suffix = true, $domain = false)`生成，注意：这是只针对插件访问URL的，主程序URL请用框架自带函数。如果不喜欢用函数的可以使用`\drcms5\addon\util\AddonUrl::build()`方法生成，效果是一致的。

### 指令

为了方便测试，我特地做了一个简单的指令。

```shell 
# 插件列表
php think draddon list
```

```shell
# 安装install、卸载uninstall、启动enable、关闭disable
# --force 可选，强制执行
# --addon=addon_name 必填，插件名
php think draddon --addon=drmo install
```

