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
namespace Hummer\Component\Cache;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class Cache_File implements ICache{

    /**
     *  @var $sCacheDir Cache Dir
     **/
    protected $sCacheDir;

    /**
     *  @var Private key
     **/
    const __CACHE__KEY__ = '#(*@#&*@@)!_)#+';

    public function __construct($sCacheDir)
    {
        if (!file_exists($sCacheDir) || !is_writable($sCacheDir)) {
            throw new \InvalidArgumentException('[cache] : File Error(not exists or unwritable)');
        }
        $this->sCacheDir = $sCacheDir;
    }

    /**
     *  Cache Data
     *  Current Only For : array, int, string
     **/
    public function store($sKey, $mVal, $iExpire=null)
    {
        if (is_resource($mVal)) {
            throw new \InvalidArgumentException('[Cache] : ERROR serialize can a resource');
        }
        return !!file_put_contents($this->getStoreFile($sKey), sprintf('%s:%s',
            Helper::TOOP($iExpire, time() + $iExpire, time() + 86400),
            serialize($mVal)
        ));
    }

    /**
     *  Get Cache
     **/
    public function fetch($sKey, $bGC = true)
    {
        $sStoreFile = $this->getStoreFile($sKey);
        if (!file_exists($sStoreFile)) {
            return null;
        }
        $sContent   = file_get_contents($sStoreFile);
        $iExpire    = substr($sContent, 0, strpos($sContent, ':'));
        $mStoreData = unserialize(substr($sContent, strpos($sContent, ':') + 1));
        //expire
        if ($iExpire < time()) {
            if ($bGC) $this->delete($sKey);
            return null;
        }
        return $mStoreData;
    }

    /**
     *  Del Cache
     **/
    public function delete($sKey)
    {
        if(file_exists($this->getStoreFile($sKey))){
            @unlink($this->getStoreFile($sKey));
        }
    }

    /**
     *  Get Save File
     **/
    protected function getStoreFile($sKey)
    {
        return sprintf('%s%s',
            Helper::TrimEnd($this->sCacheDir, '/', 'r'),
            md5($sKey . self::__CACHE__KEY__)
       );
    }
}
