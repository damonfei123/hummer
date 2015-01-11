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
use Hummer\Component\Helper\Helper;

class Model{

    /**
     *  @var $CURD  Hummer\Component\RDS\CURD
     **/
    public $CURD;

    /**
     *  @var $sTable Table
     **/
    public $sTable;

    /**
     *  @var $sItemClassName  Hummer\Component\RDS\Model\Item;
     **/
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
        $bAppItem = isset($aConfig['item_class']) && $aConfig['item_class'];
        $this->sItemClassName = sprintf('%s%s%s',
            Helper::TOOP($bAppItem, $sCalledNS, __NAMESPACE__),
            '\\',
            Arr::get($aConfig, 'item_class', 'Item')
        );
        #table
        $this->setTable($sModelName);
        $this->aConfig = $aConfig;

        #primary key
        if (isset($aConfig['pk'])) {
            $this->CURD->sPrimaryKey = $aConfig['pk'];
        }
    }

    public function setTable($sModelName)
    {
        $sTable = Arr::get($this->aConfig, 'table', strtolower($sModelName));
        $this->CURD->table($sTable);
    }

    public function initModel($sModelName)
    {
        $this->CURD->resetCondition();
        $this->setTable($sModelName);
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
        $aItems   = $this->CURD->querySmarty($mWhere);
        $aGroup   = array();
        foreach ($aItems as $aItem) {
            $aGroup[] = new $this->sItemClassName($aItem, $this);
        }
        return $aGroup;
    }

    /**
     *  Batch Save Data
     *  Use Transaction
     **/
    public function batchSave(array $aSaveData=array(), $iChunk = 1000)
    {
        if (count($aSaveData) == 0) {
            return true;
        }
        $aColumnInfo = array_map(array($this->CURD, '_addQuote'), array_keys($aSaveData[0]));
        //Column
        $sBaseSQL = sprintf('INSERT INTO %s(%s) VALUES',
            $this->CURD->getRealMapTable(),
            join(',', $aColumnInfo)
        );
        $iChunkNum    = 0;
        $bChunkSave   = true;
        $sChunkColumn = sprintf('(%s)',
            implode(',', array_pad(array(), count($aColumnInfo), '?'))
        );
        $this->CURD->begin();
        while ($aChunkData=array_slice($aSaveData, $iChunkNum * $iChunk, $iChunk))
        {
            $aChunkBind   = array();
            $aChunkColumn = array_pad(array(), count($aChunkData), $sChunkColumn);
            foreach ($aChunkData as $aCData) {
                $aChunkBind = array_merge($aChunkBind, array_values($aCData));
            }
            $sChunkSQL = sprintf('%s%s',$sBaseSQL, implode(',', $aChunkColumn));
            if(!($bChunkSave=$this->CURD->exec($sChunkSQL, $aChunkBind))){
                goto END;
            }
            $iChunkNum++;
        }

        END:
        $bChunkSave ? $this->CURD->commit() : $this->CURD->rollback();
        return $bChunkSave;
    }
    public function batchAdd(array $aSaveData=array(), $iChunk = 1000)
    {
        return $this->batchSave($aSaveData, $iChunk);
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
            throw new \BadMethodCallException('[Model] : method{'.$sMethod.'} error !!! ');
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
