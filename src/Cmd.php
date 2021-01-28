<?php

namespace drcms5\addon;

use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\migration\Command;

class Cmd extends Command
{
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
        } else if ($list) {
            $this->addon_list($list);
        }
    }

    protected function exec_addon($addon_name)
    {
        if (!$addon_name) {
            throw new AddonException('请填入插件名');
        }
        $action = $this->input->getArgument('action');
        $force = $this->input->getArgument('force');
        $queue  = explode('-', $action);
        foreach (['install', 'uninstall', 'enable', 'disable'] as $act) {
            if (in_array($act, $queue)) {
                $this->output->writeln($addon_name . ' ' . $act . ' running');
                $return = Service::$act($addon_name, );
                if (is_countable($return)) {
                    $this->output->writeln(json_encode($return, JSON_UNESCAPED_UNICODE));
                } else if (is_string($return)) {
                    $this->output->writeln($return);
                } else {
                    $this->output->writeln('return data ' . var_export($return, true));
                }
            }
        }
    }

    protected function addon_list($status)
    {
        $return = Service::list($status);
        foreach ($return as $name => $item) {
            $this->output->writeln($name . ':');
            $this->output->writeln(json_encode($item, JSON_UNESCAPED_UNICODE));
        }
    }
}