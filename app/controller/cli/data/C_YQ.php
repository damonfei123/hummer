<?php
namespace App\controller\cli\data;

use App\opt\TecLoginLog;
use App\helper\IP;
use Hummer\Component\Helper\Arr;
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

    public function actionJiShuYuan()
    {
        $TecLoginLog = new TecLoginLog();
        L('开始分析文件');
        $aData = $TecLoginLog->analyze();
        L('查找技术员');
        $i = 0; $iChunk = 1000;
        while ($aRows=DB()->getUser('u', 'slave')->where(array(
            'u.zone_user_id in' =>
                [ 10007, 10006, 17491, 27562, 87403, 58401, 54294, 60454, 73112, 123596 ]
            )
            )->limit(($i++)*$iChunk, $iChunk)
            ->select('u.id,u.name,u.province, u.city, u.county, u.promote_way, u.status ,
                     u1.id as cid, u1.name as cname, u2.name as zname')
            ->left('user u1 on u.parent_id = u1.id')
            ->left('user u2 on u1.parent_id = u2.id')->findMulti()) {
            //技术员ID
            foreach ($aRows as $Row) {
                //防止 MYSQL断线
                DB()->getUser('', 'slave')->find(1);
                $aFileLine   = array();
                $aFileLine[] = $iUserID   = $Row->id;//id
                $aFileLine[] = $sUserName = $Row->name;//技术员姓名
                $aFileLine[] = $sCName    = $Row->cname;//渠道经理姓名
                $CAuth     = DB()->get('shop_verify_auth')->find(array('user_id' => $Row->cid));
                $aCAuth    = array();//渠道经理管理省份
                if (!MEmpty($CAuth)) {
                    $aCAuth = unserialize($CAuth->auth);
                }
                $aFileLine[] = implode(' | ', $aCAuth);
                $aFileLine[] = $sZName    = $Row->zname;//所属大区
                //技术员自选省市区县
                $aFileLine[] = $Row->province;
                $aFileLine[] = $Row->city;
                $aFileLine[] = $Row->county;
                //系统自动匹配市区县
                $sAutoIP = $TecLoginLog->getAutoPC(Arr::get($aData, $Row->id, array()));
                $aAutoPC = $sAutoIP ? IP::IPToArea($sAutoIP) : array();
                $aAutoPC = array_shift($aAutoPC);
                //系统匹配省份
                $aFileLine[] = $sAutoProvince = Arr::get($aAutoPC,'province', '');
                $aFileLine[] = $sAutoCity     = Arr::get($aAutoPC,'city', '');
                //自选与系统的省是否匹配
                $aFileLine[] = $Row->province && $sAutoProvince && strpos($Row->province, $sAutoProvince) !== false ? '是' : '否';
                //管理与自选的省是否匹配
                $sManagerMatch = '否';
                //管理与系统的省是否匹配
                $sAuthMatch = '否';
                foreach ($aCAuth as $province) {
                    if($Row->province && strpos($province, $Row->province) !== false){
                        $sManagerMatch = '是';
                    }
                    if ($sAutoProvince && strpos($province, $sAutoProvince) !== false) {
                        $sAuthMatch = '是';
                    }
                }
                $aFileLine[] = $sManagerMatch;
                $aFileLine[] = $sAuthMatch;
                //技术员地/网推
                $aFileLine[] = $Row->promote_way ==  256 ? '地推' : '网推';
                //冻结
                $aFileLine[] = $Row->status ==  0 ? '正常' : '冻结';
                parent::sendFile('jishuyuan.csv', implode(',', $aFileLine));
            }
        }
    }
}
