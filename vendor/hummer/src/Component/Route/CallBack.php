<?php
namespace Hummer\Component\Route;

use Hummer\Component\Helper\Arr;

class CallBack{

    protected $mCallable;

    public function setCBObject($sControllerPath, $sAction)
    {
        $this->mCallable = array($sControllerPath, $sAction);
        return $this;
    }

    public function call()
    {
        $mClassOrObject = $this->mCallable[0];
        if (is_string($mClassOrObject)) {
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
            $bContinue = call_user_func(array($mClassOrObject, $sBefore));
        }
        if ($bContinue !== false) {
            $bContinue = call_user_func($this->mCallable);
        }
        if ($bContinue !== false && $sAfter !== null) {
            call_user_func(array($mClassOrObject, $sAfter));
        }
    }
}
