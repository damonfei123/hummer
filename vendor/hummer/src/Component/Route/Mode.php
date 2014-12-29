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
use Hummer\Component\Helper\Helper;

class Mode{

    public static function Http_Page(
        $REQ,
        $RES,
        $sControllerPath,
        $sControllerPre,
        $sActionPre,
        array $aDefaultCA = array('main', 'default')
    ) {
        $sURL = Helper::TrimInValidURI(Arr::get(parse_url($REQ->getRequestURI()),'path',''));
        $aURLPATH = explode('/', strtolower(substr($sURL,1)));
        #Action
        $sURIAction = array_pop($aURLPATH);
        $sURIAction = Helper::TOOP($sURIAction === '', $aDefaultCA[1], $sURIAction);
        $sAction    = $sActionPre . ucfirst(Helper::ReplaceLineToUpper($sURIAction));
        if (count($aURLPATH) === 0) {
            array_unshift($aURLPATH, $aDefaultCA[0]);
        }
        $sControllerName    = ucfirst(array_pop($aURLPATH));
        $sControllerDepth   = Helper::TrimEnd(implode('\\', $aURLPATH),'\\');
        $sControllerPathPre = sprintf('%s%s%s%s',
            $sControllerPath,
            $sControllerDepth,
            $sControllerPre,
            $sControllerName
        );
        if(($sRequestMethod=$REQ->getRequestMethod()) !== 'GET'){
            $sAction .= '_' . $sRequestMethod;
        }
        $CallBack = new CallBack();
        $CallBack->setCBObject($sControllerPathPre, $sAction);

        return $CallBack;
    }



    /**
     *  Route mode
     *  Test.controller.action
     **/
    public static function Http_Cli(
        $aArgv,
        $sControllerPath,
        $sControllerPre,
        $sActionPre,
        array $aDefaultCA = array('main', 'default')
    ) {
        $aParam     = isset($aArgv[2]) ? (array)json_decode($aArgv[2], true) : array();
        $sRoute     = Helper::TrimInValidURI($aArgv[1], '..', '.');
        $aURLPATH   = explode('.', $sRoute);
        #Action
        $sURIAction = array_pop($aURLPATH);
        $sURIAction = Helper::TOOP($sURIAction === '', $aDefaultCA[1], $sURIAction);
        $sAction    = $sActionPre . ucfirst(Helper::ReplaceLineToUpper($sURIAction));
        if (count($aURLPATH) === 0) {
            array_unshift($aURLPATH, $aDefaultCA[0]);
        }
        $sControllerName    = ucfirst(array_pop($aURLPATH));
        $sControllerDepth   = Helper::TrimEnd(implode('\\', $aURLPATH),'\\');
        $sControllerPathPre = sprintf('%s%s%s%s',
            $sControllerPath,
            $sControllerDepth,
            $sControllerPre,
            $sControllerName
        );

        $CallBack = new CallBack();
        $CallBack->setCBObject($sControllerPathPre, $sAction, $aParam);
        return $CallBack;
    }
}
