<?php
namespace Hummer\Component\Redis;

use Hummer\Component\Helper\Helper;
use Hummer\Component\Event\Event;

class Redis{

    protected $Instance = null;
    protected $Redis    = null;
    protected $aConfig;


    function __construct(array $aConfig) {
        Event_Register::register_All();
        $this->aConfig  = $aConfig;
    }

    public function getInstance()
    {
        if (!$this->Redis) {
            $this->Redis = new \Redis();
            call_user_func_array(
                array(
                    $this->Redis,
                    Helper::TOOP(isset($this->aConfig['pconnect']),'pconnect','pconnect')
                ),
                $this->aConfig['server']
            );
        }
        return $this->Redis;
    }

    public function __call($sMethod, $aArgs=array())
    {
        Event::call(Event_Register::E_ALL_BEFORE, $sMethod, $aArgs);
        $mResult = call_user_func_array(array($this->getInstance(),$sMethod), $aArgs);
        Event::call(Event_Register::E_ALL_AFTER, $mResult, $sMethod, $aArgs);
    }
}
