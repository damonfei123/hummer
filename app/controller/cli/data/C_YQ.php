<?php
namespace App\controller\cli\data;

use App\system\controller\Cli_Base;

class C_YQ extends Cli_Base{

    public function __before__()
    {
        $this->memLimit();
    }

    public function actionLV()
    {
        L('开始查LV下的技术员和渠道经理');
        $Users = DB()->getUser('u', 'slave')
            ->select('u.id,u.name,u.province,u.level, u.city,u2.id as pid, u2.name as pname')
            ->left('user u2 on u.parent_id = u2.id')
            ->findMulti(array('u.zone_user_id' => 10006));

        L('开始查LV下的技术员和渠道经理结束');
        $iTotal = count($Users); $i = 1;
        foreach ($Users as $User) {
            L(sprintf('开始跑用户%s/%s', $i++, $iTotal));
            $aRow['uid']      = $User->id;
            $aRow['level']    = $User->level == 4 ? '技术员' : '渠道经理';
            $aRow['province'] = $User->province ? $User->province : '-';
            $aRow['city']     = $User->city ? $User->city : '-';
            $aRow['pid']      = $User->level == 4 ? $User->pid : '-';
            $aRow['pname']    = $User->level == 4 ? $User->pname : '-';

            $IPLog = DB()->getIPLog()->find($User->id);
            $aRow['sRegProvince'] = MEmpty($IPLog) ? '无' : $IPLog->region;
            $aRow['sRegCity']     = MEmpty($IPLog) ? '无' : $IPLog->city;
            parent::sendFile('lv.csv', implode(',', $aRow));
        }
    }
}
