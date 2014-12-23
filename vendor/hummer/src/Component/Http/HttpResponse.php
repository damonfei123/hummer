<?php
namespace Hummer\Component\Http;

class HttpResponse{

    protected $BagHeader;

    public function __construct()
    {
        $this->BagHeader = new Bag_Base();
    }

    protected $iHttpStatus = 200;
    public function setStatus($iHttpStatus=200)
    {
        $this->iHttpStatus = $iHttpStatus;
    }

     protected $sProtocol = 'HTTP/1.1';
     public function setProtocol($sProtocol='HTTP/1.1')
     {
         $this->sProtocol = $sProtocol;
     }

     protected $sContent;
     public function getContent()
     {
         return $this->sContent;
     }
     public function setContent($sContent='')
     {
         $this->sContent = $sContent;
     }

    public function setHeader($mKeyOrKVMap, $mValue=null, $bOverWrite=true)
    {
        $this->BagHeader->set($mKeyOrKVMap, $mValue, $bOverWrite);
    }

    public function setHeaderRedirect($sURL, $iCode = null , $bOverWrite=true)
    {
        if ($iCode !== null) {
            $this->iHttpStatus = $iCode;
        }
        $this->BagHeader->set(array('Location' => $sURL), $bOverWrite);
    }

    protected $aPreCookie = array();
    public function setCookie(
        $sName,
        $sValue,
        $iExpire=null,
        $sPath=null,
        $sDomain=null,
        $bSecure=false,
        $bHttpOnly=false
    ) {
        $this->aPreCookie[$sName] = array(
            'value'     => $sValue,
            'expire'    => $iExpire,
            'path'      => $sPath,
            'domain'    => $sDomain,
            'secure'    => $bSecure,
            'is_httponly' => $bHttpOnly
        );
        return $this;
    }

    public function send()
    {
        $this->sendHeader();
        $this->sendContent();
    }

    public function sendHeader()
    {
        if (headers_sent()) {
            trigger_error('header has sent', E_USER_NOTICE);
            return;
        }
        if ($this->iHttpStatus != 200) {
            header(sprintf('%s %d %s',
                $this->sProtocol,
                $this->iHttpStatus,
                HttpStatus::getStatusString($this->iHttpStatus))
            );
        }

        foreach ($this->BagHeader->aData as $sK => $sV) {
            header(sprintf('%s:%s', $sK, $sV));
        }
        #set cookie
        foreach ($this->aPreCookie as $sName => $aCookie) {
            setCookie(
                $sName,
                $aCookie['value'],
                $aCookie['expire'],
                $aCookie['path'],
                $aCookie['domain'],
                $aCookie['secure'],
                $aCookie['is_httponly']
            );
        }
    }

    public function sendContent()
    {
        echo $this->sContent;
    }
}