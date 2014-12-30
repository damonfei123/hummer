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
namespace Hummer\Component\Http;

use Hummer\Component\Helper\Arr;

class Bag_Param extends Bag_Base{

    /**
     *  @var $aData Params
     **/
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
