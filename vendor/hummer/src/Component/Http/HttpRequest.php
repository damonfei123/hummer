<?php
namespace Hummer\Component\Http;

use Hummer\Component\Helper\Arr;

class HttpRequest{

    protected $aFILE;
    protected $aSERVER;
    protected $BagPOST;
    protected $BagGET;
    protected $BagCOOKIE;
    protected $BagREQUEST;


    function __construct(
        $aSERVER  = null,
        $aPOST    = null,
        $aGET     = null,
        $aCOOKIE  = null,
        $aFILE    = null,
        $aREQUEST = null
    ) {
        $this->aFILE      = $aFILE    === null ? $_FILES   : $aFILE;
        $this->aSERVER    = $aSERVER  === null ? $_SERVER  : $aSERVER;
        $this->BagPOST    = new Bag_Param($aPOST    === null ? $_POST    : $aPOST);
        $this->BagGET     = new Bag_Param($aGET     === null ? $_GET     : $aGET);
        $this->BagCOOKIE  = new Bag_Param($aCOOKIE  === null ? $_COOKIE  : $aCOOKIE);
        $this->BagREQUEST = new Bag_Param($aREQUEST === null ? $_REQUEST : $aREQUEST);
    }

    public function getG($mKeyOrKeys)
    {
        return $this->BagGET->get($mKeyOrKeys);
    }

    public function getSRV($mKey)
    {
        return Arr::get($this->aSERVER, $mKey, null);
    }

    public function getP($mKeyOrKeys)
    {
        return $this->BagPOST->get($mKeyOrKeys);
    }

    public function getC($mKeyOrKeys)
    {
        return $this->BagCOOKIE->get($mKeyOrKeys);
    }
    public function getGP($mKeyOrKeys)
    {
        return $this->BagREQUEST->get($mKeyOrKeys);
    }

    public function getRequestMethod()
    {
        return strtoupper($this->aSERVER['REQUEST_METHOD']);
    }

    public function getRequestURI()
    {
        return $this->aSERVER['REQUEST_URI'];
    }

    public function getQueryString()
    {
        return $this->aSERVER['QUERY_STRING'];
    }

    public function getProtocol()
    {
        return $this->aSERVER['SERVER_PROTOCOL'];
    }

    public function getHeader($sVar)
    {
        $sKeyName = 'HTTP_'.strtolower($sVar);
        return Arr::get($this->aSERVER, $sKeyName, null);
    }

    public function isAjax()
    {
        return strtolower($this->getHeader('X_Requested_With')) === 'xmlhttprequest';
    }

    /**
     *  get client ip
     *
     **/
    public function getClientIP()
    {
        $ip = null;
        $sHttpClientIP  = getenv('HTTP_CLIENT_IP');
        $sHttpXForWard  = getenv('HTTP_X_FORWARDED_FOR');
        $sRemoteAddr    = getenv('REMOTE_ADDR');

        if ($sHttpClientIP && strcasecmp($sHttpClientIP, 'unknown')){
            $ip = $sHttpClientIP;
        } elseif ($sHttpXForWard && strcasecmp($sHttpXForWard, 'unknown')){
            $ip = $sHttpXForWard;
        } elseif ($sRemoteAddr && strcasecmp($sRemoteAddr, 'unknown')){
            $ip = $sRemoteAddr;
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] &&
            strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')
        ){
            $ip = $_SERVER['REMOTE_ADDR'];
        } else{
            $ip = 'unknown';
        }
        return $ip !== 'unknown' ? $ip : null;
    }
}
