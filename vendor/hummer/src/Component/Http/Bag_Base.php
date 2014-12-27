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

class Bag_Base implements \ArrayAccess, \Countable {

    function __construct(array $aData = array()) {
        $this->aData = $aData;
    }

    public function set($mKeyOrKVMap, $mValue = null, $bOverWriteIfExists = true)
    {
        if (is_array($mKeyOrKVMap)) {
            $this->aData = $bOverWriteIfExists ?
                array_replace($this->aData, $mKeyOrKVMap) :
                array_merge($this->aData, $mKeyOrKVMap);
        }else{
            if ($bOverWriteIfExists || !isset($this->aData[$mKeyOrKVMap])) {
                $this->aData[$mKeyOrKVMap] = $mValue;
            }
        }
    }

    /**
     * Assigns a value to the specified offset
     *
     * @param string The offset to assign the value to
     * @param mixed  The value to set
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetSet($offset,$value) {
        $this->aData[$offset] = $value;
    }

    /**
     * Whether or not an offset exists
     *
     * @param string An offset to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function offsetExists($offset) {
        return isset($this->aData[$offset]);
    }

    /**
     * Unsets an offset
     *
     * @param string The offset to unset
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->aData[$offset]);
        }
    }

    /**
     * Returns the value at specified offset
     *
     * @param string The offset to retrieve
     * @access public
     * @return mixed
     * @abstracting ArrayAccess
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->aData[$offset] : null;
    }

    public function count()
    {
        return count($this->aData);
    }
}
