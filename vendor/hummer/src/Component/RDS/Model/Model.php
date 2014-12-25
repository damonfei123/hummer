<?php
namespace Hummer\Component\RDS\Model;

use Hummer\Component\RDS\CURD;
use Hummer\Component\Helper\Arr;

class Model{

    public $CURD;
    public $sTable;
    public $sItemClassName;

    public function __construct(
        $sModelName,
        $CURD,
        $aConfig,
        $Factory
    ) {
        $this->CURD = $CURD;
        $sCalledClassName = get_called_class();

        $sCalledNS = '';
        if(false !== ($iPos = strrpos($sCalledClassName, '\\'))){
            $sCalledNS = substr($sCalledClassName, 0, $iPos);
        }
        #Item class
        if (false !== ($iPos=strpos($sModelName, '|'))) {
            $sItemModelName = substr($sModelName, 0, $iPos);
        }else{
            $sItemModelName = $sModelName;
        }
        $this->sItemClassName = sprintf('%s%s%s',
            $sCalledNS,
            '\\',
            Arr::get($aConfig, 'item_class', 'Item_'.ucfirst($sItemModelName))
        );
        #table
        $sTable = Arr::get($aConfig, 'table', strtolower($sModelName));
        $this->CURD->table($sTable);

        #primary key
        if (isset($aConfig['pk'])) {
            $this->CURD->sPrimaryKey($aConfig['pk']);
        }
    }

    public function find($mWhere=null)
    {
        $aItem = $this->CURD->limit(1)->querySmarty($mWhere);
        return empty($aItem) ? null : new $this->sItemClassName(array_shift($aItem), $this);
    }

    public function findCustom($mWhere=null)
    {
        return $this->CURD->querySmarty($mWhere);
    }

    public function findMulti($mWhere=null)
    {
        $aItems = $this->CURD->forceSelectPK()->querySmarty($mWhere);
        $aGroup = array();
        foreach ($aItems as $aItem) {
            $mPK = $aItem[$this->sPrimaryKey];
            //if ($this->CURD->bTmpSelectPK) unset($aItem[$this->sPrimaryKey]);
            $aGroup[$mPK] = new $this->sItemClassName($aItem, $this);
        }
        return $aGroup;
    }

    public function __get($sVarName)
    {
        return property_exists($this->CURD, $sVarName) ?  $this->CURD->$sVarName : null;
    }

    public function __set($sK, $mV)
    {
        $this->CURD->$sK = $mV;
    }

    public function __call($sMethod, $aArgv)
    {
        if (!method_exists($this->CURD, $sMethod)) {
            throw new \BadMethodCallException('[CURD] : method{'.$sMethod.'} error !!! ');
        }
        $mResult = call_user_func_array(array($this->CURD, $sMethod), $aArgv);
        if (is_object($mResult) && $mResult instanceof CURD) {
            return $this;
        }
        return $mResult;
    }

    /**
     *  Deep clone
     **/
    public function __clone()
    {
        $this->CURD = clone $this->CURD;
    }
}
