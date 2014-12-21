<?php
namespace Hummer\Component\RDS;

use Hummer\Component\Helper\Packer;
use Hummer\Component\Helper\Arr;

class CURD {

    public $sDSN        = null;
    public $Instance    = null;
    public $aOption;

    public $bTmpSelectPK = false;

    public $sPrimaryKey  = 'id';

    public $sTable;
    public $aWhere      = array();
    public $aData       = array();
    public $sSelect     = '*';
    public $sJoinTable  = '';
    public $sForceIndex ='';
    public $sLimit;
    public $sGroupBy;
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

    public function forceIndex($sIndexName)
    {
        $this->sForceIndex = sprintf('force index(`%s`)',$sIndexName);
        return $this;
    }

    public function where($mWhere=null)
    {
        if (!is_null($mWhere)) {
            $this->aWhere = is_array($mWhere) ? $mWhere : array($this->sPrimaryKey => $mWhere);
        }
        return $this;
    }

    public function table($sTable)
    {
        $this->sTable = $sTable;
        return $this;
    }

    public function select($sSelect)
    {
        $this->sSelect = $sSelect;
        return $this;
    }
    public function forceSelectPK()
    {
        $aSelect = explode(',', $this->sSelect);
        if ($this->sSelect != '*' && !in_array($this->sPrimaryKey, $aSelect)) {
            $aSelect[] = $this->sPrimaryKey;
            $this->bTmpSelectPK = true;
            $this->sSelect      = join(',', $aSelect);
        }
        return $this;
    }

    public function limit($iStart, $iOffset=null)
    {
        $this->sLimit = is_null($iOffset) ?
            sprintf(' LIMIT %d ', $iStart) :
            sprintf(' LIMIT %d, %d', $iStart, $iOffset);
        return $this;
    }

    public function data($aData)
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

    public function group($sColumn)
    {
        $this->sGroupBy = sprintf(' GROUP BY %s ' , $sColumn);
        return $this;
    }

    public function exec($sSQL, $aArgs=array())
    {
        $STMT = $this->Instance->prepare($sSQL);
        return $STMT->execute($aArgs);
    }

    public function querySmarty(
        $mWhere=null,
        $iFetchMode=\PDO::FETCH_ASSOC
    ){
        if (!is_null($mWhere)) $this->where($mWhere);
        $sSQL = self::buildQuerySQL($aArgs);
        $STMT = $this->Instance->prepare($sSQL);
        $STMT->execute($aArgs);
        $STMT->setFetchMode($iFetchMode);
        return $STMT->fetchAll();
    }

    public function explain(
        $mWhere=null,
        $iFetchMode=\PDO::FETCH_ASSOC
    ) {
        if (!is_null($mWhere)) $this->where($mWhere);
        $sSQL = trim(sprintf('explain %s',self::buildQuerySQL($aArgs)));
        $STMT = $this->Instance->prepare($sSQL);
        $STMT->execute($aArgs);
        $STMT->setFetchMode($iFetchMode);
        $sEndSQL = self::buildEndSQL(str_replace('explain ','',$sSQL), $aArgs);
        $aResult = $STMT->fetchAll();
        array_unshift($aResult, $sEndSQL);
        return $aResult;
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

    public function findCount($mWhere=null)
    {
        if (!is_null($mWhere)) $this->where($mWhere);
        $this->select('count(1) as total');
        $sSQL = self::buildQuerySQL($aArgs);
        $STMT = $this->Instance->prepare($sSQL);
        $STMT->execute($aArgs);
        $mResult = $STMT->fetch();
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
        return sprintf('SELECT %s FROM %s %s %s WHERE %s %s %s',
            $this->sSelect ? $this->sSelect : '*',
            $this->sTable,
            $this->sForceIndex,
            $this->sJoinTable,
            self::buildCondition($this->aWhere, $aArgs),
            $this->sGroupBy,
            $this->sLimit
        );
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
            $this->sTable,
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
    public static function buildCondition($aWhere, &$aArgs = array())
    {
        # 为空直接返回1
        if (empty($aWhere)) return 1;

        # 提取出条件关系
        $aWhereBuild = array();
        if (isset($aWhere[-1])) {
            $sRelation = strtoupper($aWhere[-1]);
            unset($aWhere[-1]);
        } else {
            $sRelation = 'AND';
        }

        # 遍历条件
        foreach ($aWhere as $sK => $mV) {
            if (is_int($sK)) {
                # 如果是子条件, 递归调用
                $aWhereBuild[] = '(' . self::buildCondition($mV, $aArgs) . ')';
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
        while (strpos($sSQL, '?'))
        {
            $mParam = array_shift($aArgs);
            if (!is_int($mParam)) {
                $mParam = sprintf('"%s"', $mParam);
            }
            $sSQL = preg_replace('/\?/', $mParam, $sSQL, 1);
        }
        return $sSQL;
    }
}
