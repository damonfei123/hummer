<?php
namespace Hummer\Component\Http;

use Hummer\Component\Helper\Arr;

class Bag_Param extends Bag_Base{

    protected $aData;

    public function get($mKeyOrKeys)
    {
        if (is_array($mKeyOrKeys)) {
            return array_intersect_key($this->aData, array_flip($mKeyOrKeys));
        }else{
            return Arr::get($this->aData, $mKeyOrKeys, null);
        }
    }
}
