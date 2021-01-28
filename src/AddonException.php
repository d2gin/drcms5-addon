<?php

namespace drcms5\addon;

use think\Exception;

class AddonException extends Exception
{
    public function __construct($message, $code, $data = '')
    {
        $this->message = $message;
        $this->code    = $code;
        $this->data    = $data;
    }
}