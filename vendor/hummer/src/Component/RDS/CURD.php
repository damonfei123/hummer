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
namespace Hummer\Component\RDS;

use Hummer\Component\Helper\Packer;
use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class CURD {

    /**
     *  @var PDO Config
     **/
    public $sDSN        = null;
    public $Instance    = null;
    public $aOption;

    /**
     *  @var Same Model For Multi Query
     **/
    public $bMulti       = false;

    /**
     *  @var primary key & foreign key
     **/
    public $bTmpSelectPK = false;
    public $sPrimaryKey  = 'id';

    /**
     *  @var Table Condition
     **/
    public $sTable;
    public $aTableAsMap = array();
    public $aWhere      = array();
    public $aData       = array();
    public $sSelect     = '*';
    public $sJoinTable  = '';
    public $sForceIndex ='';
    public $sLimit;
    public $sGroupBy;
    public $sHaving;
    public $sOrder;
    public $aAopCallBack;

    public function __construct(
        $sDSN,
        $sUsername,
        $sPassword,
        $aOption,
        $aAopCallBack=array()
    ) {
        $this->sDSN         = $sDSN;
        $this->sUsername    = $sUsername;
        $this->sPassword    = $sPassword;
        $this->aOption      = $aOption;
        $this->aAopCallBack = $aAopCallBack;
        $this->Instance     = $this->getInstance();
    }

    public function getInstance()
    {
        if (is_null($this->Instance) || !($this->Instance instanceof \PDO)) {
            $this->Instance = new Packer(new \PDO(
                $this->sDSN,
                $this->sUsername,
                $this->sPassword,
                $this->aOption
            ),$this->aAopCallBack);
        }
        return $this->Instance;
    }

    /**
     *  Get PK
     **/
    public function getPrimaryKey($bSplit=false)
    {
        return Helper::TOOP($bSplit, explode(',', $this->sPrimaryKey), $this->sPrimaryKey);
    }
    /**
     *  Is PK Multi
     **/
    public function isPKMulti()
    {
        return false !== strpos($this->getPrimaryKey(), ',');
    }
    /**
     *  Parse PK Where
     **/
    public function getPKWhere($mWhere)
    {
        $aRetWhere = array();
        if ($this->isPKMulti()) {
            $aPK = $this->getPrimaryKey(true);
            if (!is_array($mWhere) || count($aPK) != count($mWhere)) {
                throw new \InvalidArgumentException(sprintf(
                    '[CURD] : Primary column[%s] Must Be Need !!!',
                    $this->getPrimaryKey())
                );
            }
            $aRetWhere = array_combine($aPK, $mWhere);
        }else{
            $aRetWhere[$this->sPrimaryKey] = $mWhere;
        }
        return $aRetWhere;
    }

    public function enableMulti()
    {
        $this->bMulti = true;
    }
    public function disableMulti()
    {
        $this->bMulti = false;
        $this->resetCondition();
    }

    public function forceIndex($sIndexName)
    {
        $this->sForceIndex = sprintf('force index(`%s`)',$sIndexName);
        return $this;
    }

    public function where($mWhere=null)
    {
        if (!is_null($mWhere) && $mWhere) {
            $this->aWhere = Helper::TOOP(
                is_int($mWhere) || isset($mWhere[0]),
                $this->getPKWhere($mWhere),
                $mWhere
            );
        }
        return $this;
    }

    public function table($sTable)
    {
        if (false !== strpos($sTable, '|')) {
            $aTable = explode('|', $sTable);
            $sTable = array_shift($aTable);
            $this->aTableAsMap[$sTable] = array_pop($aTable);
        }
        $this->sTable = $sTable;
        return $this;
    }

    public function getRealMapTable()
    {
        $sAsTable = Arr::get($this->aTableAsMap, $this->sTable, '');
        return sprintf('%s %s',$this->sTable, $sAsTable);
    }

    public function getTableAsMap()
    {
        return Arr::get($this->aTableAsMap, $this->sTable, $this->sTable);
    }

    public function select($sSelect)
    {
        $this->sSelect = $sSelect;
        return $this;
    }

    public function getSelect()
    {
        return $this->sSelect ? $this->sSelect : '*';
    }

    /**
     *  Only Single PK Be Forced
     **/
    public function forceSelectPK()
    {
        $sPK     = $this->getPrimaryKey();
        $aSelect = explode(',', $this->sSelect);
        if (!$this->isPKMulti() && !in_array($sPK, $aSelect) && $this->sSelect !== '*' &&
            false === strpos($this->sSelect, sprintf('%s.*', $this->getTableAsMap())) &&
            false === strpos($this->sSelect, sprintf('%s.%s', $this->getTableAsMap(), $sPK))
        ) {
            foreach ($this->getPrimaryKey(true) as $iK => $sPK) {
                $sPK = trim($sPK);
                if ($sPK) $aSelect[] = sprintf('%s.%s', $this->getTableAsMap(), $sPK, $iK);
            }
            $this->sSelect      = join(',', $aSelect);
            $this->bTmpSelectPK = true;
        }
        return $this;
    }

    public function limit($iStart, $iOffset=null)
    {
        $this->sLimit = ($iOffset === null) ?
            sprintf(' LIMIT %d ', $iStart) :
            sprintf(' LIMIT %d, %d', $iStart, $iOffset);
        return $this;
    }

    public function data(array $aData)
    {
        $this->aData = $aData;
        return $this;
    }


    public function join($sTable,$sJoinType='L')
    {
        $sJoinTable = '';
        switch (strtoupper($sJoinType))
        {
            case 'L': #for quicky
                $sJoinType = 'LEFT';
                break;
            case 'R':
                $sJoinType = 'RIGHT';
                break;
            case 'S':
                $sJoinType = 'STRAIGHT';
                break;
            default:
                $sJoinType = 'LEFT';
                break;
        }
        $this->sJoinTable = sprintf('%s %s JOIN %s ', $this->sJoinTable, $sJoinType, $sTable);
        return $this;
    }

    public function left($sTable)
    {
        return $this->join($sTable,'L');
    }

    public function right($sTable)
    {
        return $this->join($sTable,'R');
    }

    public function group($sColumn)
    {
        $this->sGroupBy = sprintf(' GROUP BY %s ' , $sColumn);
        return $this;
    }
    public function having($sHaving)
    {
        $this->sHaving = sprintf(' HAVING %s ', $sHaving);
        return $this;
    }

    public function exists($CURD)
    {
        if (!($CURD instanceof \Hummer\Component\RDS\Model\Model)) {
            throw new \InvalidArgumentException('[CURD] : ERROR, Param Must Be CURD OBJ');
        }
        $aArgs = array();
        $this->aWhere['__exists__'] = $CURD->getQuerySQL($aArgs);
        return $this;
    }

    public function exec($sSQL, $aArgs=array())
    {
        $STMT = $this->Instance->prepare($sSQL);
        return $STMT->execute($aArgs);
    }

    public function query($sSQL, $aArgs, $iFetchMode=\PDO::FETCH_ASSOC)
    {
        $STMT = $this->Instance->prepare($sSQL);
        $STMT->execute($aArgs);
        $STMT->setFetchMode($iFetchMode ? $iFetchMode : \PDO::FETCH_ASSOC);
        return $STMT->fetchAll();
    }

    public function querySmarty(
        $mWhere=null,
        $iFetchMode=\PDO::FETCH_ASSOC
    ){
        if (!is_null($mWhere)) $this->where($mWhere);
        $sSQL = $this->buildQuerySQL($aArgs);
        return $this->queryAndFind($sSQL, $aArgs, $iFetchMode);
    }

    public function explain(
        $mWhere=null,
        $iFetchMode=\PDO::FETCH_ASSOC
    ) {
        if (!is_null($mWhere)) $this->where($mWhere);
        $sSQL = trim(sprintf('explain %s',$this->buildQuerySQL($aArgs)));
        $aResult = $this->queryAndFind($sSQL, $aArgs, $iFetchMode);

        #sql info
        $sEndSQL = self::buildEndSQL(str_replace('explain ','',$sSQL), $aArgs);

        #table indexes
        $aIndexes = $this->queryAndFind(
            sprintf('SHOW INDEXES FROM %s',
            $this->getRealMapTable()),
            $aArgs,
            $iFetchMode
        );

        return array(
            'SQL'   => $sEndSQL,
            'TABLE INDEX' => $aIndexes,
            'EXPLAIN'     => $aResult
        );
    }

    public function save($aSaveData=array())
    {
        if ($aSaveData) {
            $this->data($aSaveData);
        }
        $aArgs       = array();
        $sSQLPrepare = $this->buildSaveSQL($aArgs);
        $STMT        = $this->Instance->prepare($sSQLPrepare);
        $STMT->execute($aArgs);
        return $this->Instance->lastInsertId();
    }

    public function add($aSaveData=array())
    {
        return $this->save($aSaveData);
    }

    public function findCount($mWhere=null)
    {
        if (!is_null($mWhere)) $this->where($mWhere);
        $this->select('count(1) as total');
        $sSQL = $this->buildQuerySQL($aArgs);
        $mResult = $this->queryAndFind($sSQL, $aArgs,null, true);
        return Arr::get($mResult, 'total', 0);
    }

    public function buildSaveSQL(&$aArgs)
    {
        $aData = $this->aData;
        if (empty($aData) || !is_array($aData)) {
            return false;
        }
        $sField = $sBindParam = '';
        foreach ($aData as $sK => $mV) {
            $sField     .= "$sK,";
            $sBindParam .= "?,";
            $aArgs[]     = $mV;
        }
        $sField     = trim($sField, ',');
        $sBindParam = trim($sBindParam, ',');
        return sprintf('INSERT INTO %s(%s) values(%s)',
            $this->sTable,
            $sField,
            $sBindParam
        );
    }

    public function buildUpdateSQL(&$aUpdateDataArg, &$aArgs)
    {
        $aUpdatePre = $aUpdateData = array();
        foreach ($this->aData as $sK => $mV) {
            if (is_int($sK)) {
                $aUpdatePre[] = $mV;
            }else{
                $sKK          = self::addQuote($sK);
                $aUpdatePre[] = "$sKK = ?";
                $aUpdateDataArg[] = $mV;
            }
        }
        return sprintf('UPDATE %s SET %s WHERE %s',
            $this->sTable,
            implode(',', $aUpdatePre),
            self::buildCondition($this->aWhere, $aArgs)
        );
    }

    public function buildQuerySQL(&$aArgs)
    {
        return sprintf('SELECT %s FROM %s %s %s WHERE %s %s %s %s',
            $this->sSelect ? $this->sSelect : '*',
            $this->getRealMapTable(),
            $this->sForceIndex,
            $this->sJoinTable,
            self::buildCondition($this->aWhere, $aArgs),
            $this->sGroupBy,
            $this->sHaving,
            $this->sLimit
        );
    }

    public function getQuerySQL()
    {
        $aArgs = array();
        return $this->buildQuerySQL($aArgs);
    }

    public function delete($mWhere=array())
    {
        if (!is_null($mWhere)) $this->where($mWhere);
        $aArgs = array();
        $sSQL  = self::buildDeleteSQL($this->aWhere, $aArgs);
        $STMT  = $this->Instance->prepare($sSQL);
        return $STMT->execute($aArgs);
    }

    public function buildDeleteSQL($aWhere, &$aArgs)
    {
        return sprintf('DELETE FROM %s WHERE %s',
            $this->getRealMapTable(),
            self::buildCondition($aWhere, $aArgs)
        );
    }

    public function update($mWhere=null) {
        if (!is_null($mWhere)) $this->where($mWhere);
        $aArgs       = $aUpdateData = array();
        $sSQLPrepare = $this->buildUpdateSQL($aUpdateData, $aArgs);
        $STMT        = $this->Instance->prepare($sSQLPrepare);
        return $STMT->execute(array_merge($aUpdateData, $aArgs));
    }

    /**
     *  事务
     **/
    public function begin()
    {
        $this->Instance->beginTransaction();
        return $this;
    }
    public function rollBack()
    {
        return $this->Instance->rollBack();
    }
    public function commit()
    {
        return $this->Instance->commit();
    }
    ///////////////////事务END/////////////////////

    /**
     * @param array $aArgs
     * @return int|string
     */
    public static function buildCondition($mWhere, &$aArgs = array())
    {
        # 为空直接返回1
        if (empty($mWhere)) return 1;

        if (is_string($mWhere)) {
            return $mWhere;
        }

        # 提取出条件关系
        $aWhereBuild = array();
        if (isset($mWhere[-1])) {
            $sRelation = strtoupper($mWhere[-1]);
            unset($mWhere[-1]);
        } else {
            $sRelation = 'AND';
        }

        # 遍历条件
        $aWhere = $mWhere;
        foreach ($aWhere as $sK => $mV) {
            if (is_int($sK)) {
                # 如果是子条件, 递归调用
                $aWhereBuild[] = '(' . self::buildCondition($mV, $aArgs) . ')';
            }elseif($sK == '__exists__'){
                $aWhereBuild[] = 'EXISTS (' . self::buildCondition($mV, $aArgs) . ')';
            } else {
                # 如果不是子条件, 解析
                list($sKey, $sOP) = array_replace(array('', '='), explode(' ', $sK, 2));
                $sKey = self::addQuote($sKey);
                $sOP  = trim(strtoupper($sOP));
                if (in_array($sOP, array('IN', 'NOT IN'))) {
                    if (empty($mV)) {
                        $aWhereBuild[] = '1';
                    } else {
                        $mV = array_unique($mV);
                        $aWhereBuild[] = sprintf('%s %s (%s)',
                            $sKey,
                            $sOP,
                            implode(',', array_fill(0, count($mV), '?'))
                        );
                        $aArgs = (array)$aArgs;
                        $aArgs = array_merge($aArgs, $mV);
                    }
                } else if('BETWEEN' == $sOP){
                    if (!is_array($mV) || count($mV) != 2) {
                        throw new \InvalidArgumentException('[CURD] : Error Params');
                    }
                    $aWhereBuild[] = "$sKey BETWEEN ? AND ?";
                    $aArgs[]       = array_shift($mV);
                    $aArgs[]       = array_shift($mV);
                }else {
                    $aWhereBuild[] = sprintf(' %s %s ? ',$sKey, $sOP);
                    $aArgs[]       = $mV;
                }
            }
        }
        #返回结果
        return implode(" $sRelation ", $aWhereBuild);
    }

    public static function addQuote($mK)
    {
        if (is_array($mK)) {
            $aKFix = array();
            foreach ($mK as $mKK => $mVV) {
                $aKFix[$mKK] = self::addQuote($mVV);
            }
            return $aKFix;
        } else {
            $aK = explode('.', $mK);
            return count($aK) === 1 ?
                self::_addQuote($aK[0]) :
                implode('.', array_map(array('Hummer\\Component\\RDS\\CURD', '_addQuote'), $aK));
        }
    }

    public static function _addQuote($sK)
    {
        return $sK[0] === ':' ? substr($sK, 1) : "`$sK`";
    }

    /**
     *  build end execute SQL
     *  @info for debug use
     **/
    public static function buildEndSQL($sSQL, $aArgs)
    {
        while (strpos($sSQL, '?')) {
            $mParam = array_shift($aArgs);
            if (!is_int($mParam)) {
                $mParam = sprintf('"%s"', $mParam);
            }
            $sSQL = preg_replace('/\?/', $mParam, $sSQL, 1);
        }
        return $sSQL;
    }

    public function queryAndFind(
        $sSQL,
        $aArgs,
        $iFetchMode=\PDO::FETCH_ASSOC,
        $bOnlyOne=false
    ) {
        $STMT = $this->Instance->prepare(Helper::TrimInValidURI($sSQL, '  ', ' '));
        $STMT->execute($aArgs);
        $STMT->setFetchMode($iFetchMode ? $iFetchMode : \PDO::FETCH_ASSOC);
        if (!$this->bMulti) {
            $this->resetCondition();
        }
        return $bOnlyOne ? $STMT->fetch() : $STMT->fetchAll();
    }

    public function resetCondition()
    {
        $this->bTmpSelectPK = false;

        $this->aTableAsMap = array();
        $this->aWhere      = array();
        $this->aData       = array();
        $this->sSelect     = '*';
        $this->sJoinTable  = '';
        $this->sForceIndex = '';
        $this->sLimit      = '';
        $this->sGroupBy    = '';
        $this->sHaving     = '';
        $this->sOrder      = '';
    }
}
