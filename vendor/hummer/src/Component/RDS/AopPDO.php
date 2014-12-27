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
use Hummer\Component\Context\Context;

class AopPDO {
    public static $aAopPreExecCost = array(
        'prepare.after' => array(
            array('Hummer\Component\RDS\AopPDO', 'pdoAfter')
        )
    );

    public static function stmtExecBefore($Obj, $sMethod, $aArgv, \stdClass $Result)
    {
        $Log = Context::getInst()->Log;
        $iS  = microtime(true);
        $mRS = call_user_func_array(array($Obj, $sMethod), $aArgv);
        $iDiff = microtime(true) - $iS;
        $Log->info(
            '[SQL] : {cost}; {sql}; {bind}',
            array(
                'sql'  => $Obj->queryString,
                'bind' => empty($aArgv[0]) ? '' : $aArgv[0],
                'cost' => sprintf('%.2f ms', $iDiff * 1000)
            )
        );
        $Result->value = $mRS;
    }

    public static function pdoAfter($Obj, $sMethod, $aArgv, \stdClass $Result)
    {
        $Obj = $Result->value;
        if ($Obj instanceof \PDOStatement) {
            $Result->value = new Packer($Obj, array(
                'execute.before' => array(
                    array('Hummer\Component\RDS\AopPDO', 'stmtExecBefore')
                )
            ));
        }
    }
}
