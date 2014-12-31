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
namespace Hummer\Component\Helper;

class Packer{
    private $obj;
    private $aAopCallBack;

    /**
     *  execute.after = array(array(Hummer\Component\RDS\AopPDO', 'pdoAfter'))
     **/
    public function __construct($obj, $aAopCallBack=array())
    {
        $this->obj = $obj;
        if($aAopCallBack && is_array($aAopCallBack)) foreach ($aAopCallBack as $sExplain => $aCB) {
            $this->addCallBack($sExplain, $aCB);
        }
    }

    protected function addCallBack($sExplain, $aCB)
    {
        $aArr = explode('.', $sExplain);
        $sMethod = $aArr[0];
        $sExecMethod = $aArr[1];
        $this->aAopCallBack[$sMethod][$sExecMethod] = $aCB[0];
    }

    public function __get($sKey)
    {
        return $this->obj->$sKey;
    }

    public function __set($sKey, $mValue)
    {
        $this->obj->$sKey = $mValue;
    }

    public function __call($sMethod, $aArgv=array())
    {
        $Result = new \stdClass();
        $Result->value = null;
        if (isset($this->aAopCallBack[$sMethod]['before'])) {
            call_user_func(
                $this->aAopCallBack[$sMethod]['before'],
                $this->obj,
                $sMethod,
                $aArgv,
                $Result
           );
        }
        if ($Result->value == null) {
            $Result->value = call_user_func_array(array($this->obj, $sMethod), $aArgv);
        }
        if (isset($this->aAopCallBack[$sMethod]['after'])) {
            call_user_func(
                $this->aAopCallBack[$sMethod]['after'],
                $this->obj,
                $sMethod,
                $aArgv,
                $Result
           );
        }
        return $Result->value;
    }
}
