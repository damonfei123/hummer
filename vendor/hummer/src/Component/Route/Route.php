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

class Route{
    protected $Context;

    function __construct($Context=null) {
        $this->Context = $Context;
    }

    /**
     *  RUN FOR HTTP
     *  @param $REQ     HttpRequest
     *  @param $RES     HttpResponse
     *  @param $aRule   array
     **/
    public function generateFromHttp($REQ, $RES, $aRule=array())
    {
        $aCallBack = array();
        if (!$aRule || !is_array($aRule)) {
            throw new \InvalidArgumentException('[Route] : ERROR ROUTE PARAM');
        }
        foreach ($aRule as $aV) {
            if (!is_array($aV) || count($aV) < 4) {
                throw new \DomainException('[Route] : ERROR CONFIG');
            }
            $mV              = array_shift($aV);
            $sControllerPath = array_shift($aV);
            $sControllerPre  = array_shift($aV);
            $sActionPre      = array_shift($aV);
            if (!is_null($this->Context)) {
                $this->Context->registerMulti(array(
                    'sControllerPath' => $sControllerPath,
                    'sControllerPre'  => $sControllerPre,
                    'sActionPre'      => $sActionPre
                ));
            }
            $aCallBack[] = call_user_func_array(
                $mV,
                array($REQ, $RES, $sControllerPath, $sControllerPre, $sActionPre)
            );
        }
        return $aCallBack;
    }


    /**
     *  RUN FOR CLI
     *  @param $argv    $argv
     *  @param $aRule   array
     **/
    public function generateFromCli($aArgv, $aRule=array())
    {
        $aCallBack = array();
        if (!$aRule || !is_array($aRule)) {
            throw new \InvalidArgumentException('[Route] : ERROR ROUTE PARAM');
        }
        foreach ($aRule as $aV) {
            if (!is_array($aV) || count($aV) < 4) {
                throw new \DomainException('[Route] : ERROR CONFIG');
            }
            $mV              = array_shift($aV);
            $sControllerPath = array_shift($aV);
            $sControllerPre  = array_shift($aV);
            $sActionPre      = array_shift($aV);
            if (!is_null($this->Context)) {
                $this->Context->registerMulti(array(
                    'sControllerPath' => $sControllerPath,
                    'sControllerPre'  => $sControllerPre,
                    'sActionPre'      => $sActionPre
                ));
            }
            $aCallBack[] = call_user_func_array(
                $mV,
                array($aArgv, $sControllerPath, $sControllerPre, $sActionPre)
            );
        }
        return $aCallBack;
    }

}
