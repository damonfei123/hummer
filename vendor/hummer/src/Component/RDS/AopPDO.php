<?php
namespace Hummer\Component\RDS;

use Hummer\Component\Helper\Packer;

class AopPDO {
    public static $aAopPreExecCost = array(
        'prepare.after' => array(
            array('Hummer\Component\RDS\AopPDO', 'pdoAfter')
        )
    );

    public static function stmtExecBefore($Obj, $sMethod, $aArgv, \stdClass $Result)
    {
        $iS = microtime(true);
        $mRS = call_user_func_array(array($Obj, $sMethod), $aArgv);
        fprintf(STDOUT, $Obj->queryString . "\n", microtime(true) - $iS);
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
