<?php

namespace drcms5\addon\cmd;

use drcms5\addon\Service;

trait Kernel
{
    protected function exec_addon($addon_name)
    {
        if (!$addon_name) {
            throw new AddonException('请填入插件名');
        }
        $action = $this->input->getArgument('action');
        $force  = $this->input->getArgument('force');
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