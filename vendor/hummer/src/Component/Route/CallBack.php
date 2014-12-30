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
namespace Hummer\Component\Route;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Route\RouteErrorException;

class CallBack{

    protected $mCallable;

    public function setCBObject($sControllerPath, $sAction, $aArgs=array())
    {
        $this->mCallable = array($sControllerPath, $sAction, $aArgs);
        return $this;
    }

    public function call()
    {
        $mClassOrObject = $this->mCallable[0];
        if (is_string($mClassOrObject)) {
            if (!class_exists($mClassOrObject)) {
                throw new RouteErrorException("[class] : $mClassOrObject does not exsits");
            }
            $Ref = new \ReflectionClass($mClassOrObject);
            $this->mCallable[0] = $mClassOrObject = $Ref->newInstanceArgs();
        }else{
            throw new \DomainException('[CallBack] : ERROR');
        }

        #get callable method
        $aCallableMethod = array();
        foreach ($Ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $Method) {
            $aCallableMethod[$Method->getName()] = $Method->getName();
        }

        #get Method
        $sMethod = $this->mCallable[1];
        if (!isset($aCallableMethod[$sMethod])) {
            throw new RouteErrorException(sprintf('[ROUTE] : There is no method %s in class %s',
                $sMethod,
                is_object($mClassOrObject) ? get_class($mClassOrObject) : $mClassOrObject
            ));
        }

        #aArgs
        $aArgs = (array)$this->mCallable[2];

        #before and after
        $sBefore = $sAfter = null;
        $sMethodBefore = sprintf('__before__%s__', $sMethod);
        $sMethodAfter  = sprintf('__after__%s__', $sMethod);
        $sBefore = Arr::get($aCallableMethod, $sMethodBefore, '__before__');
        $sAfter  = Arr::get($aCallableMethod, $sMethodAfter, '__after__');
        $sBefore = Arr::get($aCallableMethod, $sBefore);
        $sAfter  = Arr::get($aCallableMethod, $sAfter);

        $bContinue = true;
        if ($sBefore !== null) {
            $bContinue = call_user_func(array($mClassOrObject, $sBefore), $aArgs);
        }
        if ($bContinue !== false) {
            $bContinue = call_user_func(array(
                $this->mCallable[0],
                $this->mCallable[1]
            ), $aArgs);
        }
        if ($bContinue !== false && $sAfter !== null) {
            call_user_func(array($mClassOrObject, $sAfter), $aArgs);
        }
    }
}
