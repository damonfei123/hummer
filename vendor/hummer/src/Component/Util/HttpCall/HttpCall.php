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
namespace Hummer\Component\Util\HttpCall;

use Hummer\Component\Event\Event;
use Hummer\Component\Helper\Arr;

class HttpCall{

    /**
     *  Init CURL
     **/
    public static function initCURL()
    {
        return curl_init();
    }

    /**
     *  Curl Call By Get
     **/
    public static function callGET(
        $sUrl,
        array $aParam  = array(),
        array $aHeader = array(),
        array $aOptKV  = array()
    ) {
        $aUrlBlock = parse_url($sUrl);
        $sQuery    = Arr::get($aUrlBlock,'query','');
        parse_str($sQuery, $aQuery);
        $sUrl = self::rebuildUrl($aUrlBlock, $aParam + $aQuery);
        if ($aHeader) {
            $aOptKV[CURLOPT_HTTPHEADER] = $aHeader;
        }
        return self::call($sUrl, $aOptKV);
    }

    /**
     *  Curl Call By POST
     **/
    public static function callPOST(
        $sUrl,
        array $aParam  = array(),
        array $aHeader = array(),
        array $aOptKV  = array()
    ) {
        $aOptKV[CURLOPT_POSTFIELDS] = Helper::TOOP($aParam,http_build_query($aParam),'');
        if ($aHeader) {
            $aOptKV[CURLOPT_HTTPHEADER] = $aHeader;
        }
        $aOptKV[CURLOPT_POST] = 1;
        return self::call($sUrl, $aOptKV);
    }

    /**
     *  CURL Call
     **/
    public static function call($sUrl, array $aHeaderAndData=array(), $bEvent=true)
    {
        Event_Register::register_All($bEvent);
        $CURL = self::initCURL();
        #URL
        $aHeaderAndData[CURLOPT_URL] = $sUrl;
        #https
        if (strcasecmp(substr($sUrl,0,8), 'https://') == 0) {
            if (!isset($aHeaderAndData[CURLOPT_SSL_VERIFYHOST])) {
                $aHeaderAndData[CURLOPT_SSL_VERIFYHOST] = 0;
            }
            if (!isset($aHeaderAndData[CURLOPT_SSL_VERIFYPEER])) {
                $aHeaderAndData[CURLOPT_SSL_VERIFYPEER] = 0;
            }
        }
        #timeout
        if (!isset($aHeaderAndData[CURLOPT_TIMEOUT]) && !isset($aHeaderAndData[CURLOPT_TIMEOUT_MS])) {
            $aHeaderAndData[CURLOPT_TIMEOUT] = 10;
        }
        #return
        if (!isset($aHeaderAndData[CURLOPT_RETURNTRANSFER])) {
            $aHeaderAndData[CURLOPT_RETURNTRANSFER] = 1;
        }
        curl_setopt_array($CURL, $aHeaderAndData);
        Event::call(Event_Register::E_ALL_BEFORE);
        $mResult = curl_exec($CURL);
        Event::call(
            Event_Register::E_ALL_AFTER,
            $sUrl,
            Arr::get($aHeaderAndData, CURLOPT_POSTFIELDS, array()),
            $mResult
        );
        curl_close($CURL);
        return $mResult;
    }

    public static function rebuildUrl($aUrlBlock, array $aParam = array())
    {
        return sprintf('%s%s%s%s%s%s%s%s',
            Arr::get($aUrlBlock, 'scheme', 'http') . '://',
            isset($aUrlBlock['user']) ? $aUrlBlock['user'].':'   : '',
            isset($aUrlBlock['pass']) ? $aUrlBlock['pass'].'@'   : '',
            Arr::get($aUrlBlock, 'host', ''),
            isset($aUrlBlock['port']) ? ':'.$aUrlBlock['port']   : '',
            Arr::get($aUrlBlock, 'path', ''),
            '?' . http_build_query($aParam),
            isset($aUrlBlock['fragment'])? '#'.$aUrlBlock['fragment']   : ''
        );
    }
}
