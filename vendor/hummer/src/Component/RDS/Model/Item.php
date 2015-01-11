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
namespace Hummer\Component\RDS\Model;

use Hummer\Component\RDS\CURD;
use Hummer\Component\Helper\Arr;

class Item implements \ArrayAccess {

    /**
     *  @var $aData Row Data
     **/
    protected $aData;

    /**
     *  @var $Model
     **/
    protected $Model;

    /**
     *  @var $aNewData Update Data
     **/
    protected $aNewData;

    public function __construct($aData = array(), $Model) {
        $this->aData = $aData;
        $this->Model = $Model;
    }

    public function __toString()
    {
        return var_export($this->aData, true);
    }

    public function __set($sK, $mV)
    {
        $this->aNewData[$sK] = $mV;
    }

    public function update()
    {
        $aUpdateData = array_intersect_key($this->aNewData, $this->aData);
        if (empty($aUpdateData)) {
            return true;
        }
        $M       = $this->Model;
        $bUpdate = $M->CURD->where($this->getPrimaryKeyWhere())->data($aUpdateData)->update();
        if ($bUpdate) {
            $this->aNewData = array();
        }
        return $bUpdate;
    }

    public function delete()
    {
        return $this->Model->CURD->where($this->getPrimaryKeyWhere())->delete();
    }

    /**
     *  Get Primary Key Where
     *  All Primary Key Must In Data
     **/
    public function getPrimaryKeyWhere()
    {
        $M     = $this->Model;
        $aPK   = $M->CURD->getPrimaryKey(true);
        $aData = array_intersect_key($this->aData, array_flip($aPK));
        if (count($aData) != count($aPK)) {
            throw new \InvalidArgumentException(sprintf(
                '[Item] : Primary column[%s] Must Be Selected!!!',
                $M->CURD->getPrimaryKey())
            );
        }
        return $aData;
    }

    public function __get($sVarName)
    {
        return Arr::get($this->aData, $sVarName);
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
        $this->aNewData[$offset] = $value;
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
}
