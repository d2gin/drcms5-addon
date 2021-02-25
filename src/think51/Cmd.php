<?php

namespace drcms5\addon\think51;

use drcms5\addon\cmd\Kernel;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\migration\Command;

class Cmd extends Command
{
    use Kernel;
    protected $cmd_name = 'draddon';

    protected function configure()
    {
        $this->setName($this->cmd_name);
        $this->addOption('addon', 'a', Option::VALUE_REQUIRED, '插件名');
        $this->addOption('force');
        $this->addOption('list', 'l', Option::VALUE_OPTIONAL, '插件列表');
        $this->addArgument('action', Argument::OPTIONAL, '执行动作');
    }

    protected function execute(Input $input, Output $output)
    {
        $addon_name = $input->getOption('addon');
        $list       = $input->getOption('list');
        if ($addon_name) {
            $this->exec_addon($addon_name);
        } else if (is_numeric($list)) {
            $this->addon_list($list);
        }
    }
}