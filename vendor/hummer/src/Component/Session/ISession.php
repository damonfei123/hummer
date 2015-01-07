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

interface ISession {

    /**
     *  Open
     **/
    public function open($sPath, $sSessionName);

    /**
     *  close
     **/
    public function close();

    /**
     * Read
     **/
    public function read($sVar);

    /**
     *  write
     **/
    public function write($sVar, $mV);

    /**
     *  Destroy
     **/
    public function destroy($sVar);

    /**
     *  GC
     **/
    public function gc($maxlifetime);

}
