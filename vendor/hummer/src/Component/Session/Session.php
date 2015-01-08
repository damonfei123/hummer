<?php
/*************************************************************************************

   +-----------------------------------------------------------------------------+
   | Hummer [ Make Code Beauty And Web Easy ]                                    |
   +-----------------------------------------------------------------------------+
   | Copyright (c) 2014 https://github.com/damonfei123 All rights reserved.      |
   +-----------------------------------------------------------------------------+
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )                     |
   +-----------------------------------------------------------------------------+
   | Author: Damon <zhangyinfei313com@163.com>                                   |
   +-----------------------------------------------------------------------------+

**************************************************************************************/
namespace Hummer\Component\Session;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Suger;
use Hummer\Component\Helper\Helper;

class Session{

    /**
     *  @var Instance
     **/

    public function __construct()
    {
        $this->Instance = Suger::createObjAdaptor(__NAMESPACE__, func_get_args(), 'Session_');

        session_set_save_handler(
            array($this->Instance, 'open'),
            array($this->Instance, 'close'),
            array($this->Instance, 'read'),
            array($this->Instance, 'write'),
            array($this->Instance, 'destroy'),
            array($this->Instance, 'gc')
        );
        register_shutdown_function('session_write_close');
        session_start();
    }

    public function __destruct()
    {
        session_unset();
    }
}
