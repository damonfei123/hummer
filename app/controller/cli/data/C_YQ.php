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

    /**
     *  划转技术
     **/
    public function actionHuazhuang()
    {
        foreach(DB()->get('t_channel_apply')
            ->where(array('apply_time between' => array('2015-02-02', '2015-02-09'), 'status' => 2))
            ->select('user_id, channel_userid, zone_user_id, status, apply_time')
            ->findMulti() as $Record){
            $aData   = array();
            $User    = DB()->getUser('u')->left('user u2 on u.parent_id = u2.id')
                ->left('user u3 on u2.parent_id = u3.id')
                ->select('u.id, u.name, u2.id as cid, u2.name as cname, u3.id as zid, u3.name as zname')
                ->where(array('u.id' => $Record->user_id))
                ->find();
            $aData[] = $Record->user_id;//ID
            $aData[] = $User->name;//Name
            //原来
            $iOldPID = DB()->get('t_user_belong_change_log')
                ->where(array(
                    'new_parent_id' => $Record->channel_userid,
                    'user_id' => $Record->user_id)
                )->find()->old_parent_id;
            if (!$iOldPID) {
                L('错误:'.$Record->user_id);
                die;
            }
            $OldParent = DB()->getUser('u')->left('user u2 on u.parent_id = u2.id')
                ->select('u.id, u.name, u2.id as cid, u2.name as zname')
                ->where(array('u.id' => $iOldPID))
                ->find();
            $aData[] = $OldParent->name;
            $aData[] = $OldParent->zname;

            $aData[] = $User->cid;
            $Manager = DB()->get('shop_verify_auth')->find(array('user_id' => $User->cid));
            $aData[] = !MEmpty($Manager) ? implode('|',unserialize($Manager->auth)) : '';//管理省份
            $aData[] = $User->zname;
            $aData[] = $Record->apply_time;
            parent::sendFile('huazhuang.csv', implode(',', $aData));
        }
    }

    /**
     *  拉hao123数据
     **/
    static $aRuleType = array();
    public function actionHao123()
    {
        $sDate = '2015-02-02';
        #所有被打hao123网推用户
        L('所有被打hao123网推用户');
        $aHao123WangTui = array_keys(Arr::changeIndex(
                DB()->get('user_invalidate')->select('user_id')->findCustom() ,
        'user_id'));
        #所有安装量
        L('所有安装量');
        $OriData = DB()->get('tec_daily_data')
            ->where(array('soft_id' => 12,'date >=' => $sDate))
            ->select('user_id, date, effect_org')
            ->findCustom();
        $aOriData = array();
        foreach ($OriData as $Data) {
            $iUID  = $Data['user_id'];
            $sDate = $Data['date'];
            $aOriData[$iUID . '_' . $sDate] = $Data['effect_org'];
        }
        #用户被打hao123网推标签
        /*
        L('用户被打hao123网推标签');
        $aCheatAction = Arr::changeIndexToKVMap(DB()->get('cheat_action', 'hao123')
            ->where(array('soft' => 12))
            ->select('tt, group_concat(distinct type  Separator "|") as gtype')
            ->group('tt')
            ->findCustom(), 'tt', 'gtype');
        */

        $i = 0; $iChunk = 1000;
        L('开始读数据....');
        while($aRecord=DB()->get('hao123_compare')
            ->where(array('date >=' => $sDate))
            ->select('tt, tn, date, source_hao123, source_db')
            ->limit(($i++) * $iChunk, $iChunk)
            ->findMulti()){
            L('处理1000个用户完成');
            foreach ($aRecord as $Record) {
                $aData = array();
                $aData[] = $Record->tt;
                $aData[] = $Record->tn;
                $aData[] = $Record->date;
                $aData[] = $Record->source_db;
                $aData[] = $Record->source_hao123;
                $aData[] = isset($aOriData[$Record->tt . '_' . $Record->date]) ?
                    $aOriData[$Record->tt . '_' . $Record->date] : 0;
                $aData[] = in_array($Record->tt, $aHao123WangTui) ? '是' : '否';
                #规则
                if (in_array($Record->tt, $aHao123WangTui) && !isset($aRuleType[$Record->tt])) {
                    $aRuleType[$Record->tt] = implode('|', array_keys(Arr::changeIndex(
                        DB()->get('cheat_action', 'hao123')
                        ->where(array('soft' => 12, 'tt' => $Record->tt))
                        ->select('distinct type')
                        ->findCustom(), 'type')));
                }
                $aData[] = isset($aRuleType[$Record->tt]) ? $aRuleType[$Record->tt] : '';
                parent::sendFile('hao123.csv', implode(',', $aData));
            }
        }
    }


    /**
     *  拉周玲渠道经理有效活跃数据
     **/
    public function actionZL()
    {
        $i = 0; $iChunk = 100;
        L('1月有效技术员(去冻结)');
        //1月有效技术员(去冻结)
        $aEffectCount = Arr::changeIndexToKVMap(DB()->get('tec_daily_combine_data t')
            ->where(array(
                't.is_effect >'   => 0,
                'u.level'         => 4,
                'u.status'        => 0,
                array(
                    -1                => 'or',
                    'u.belong_type'   => 0,
                    array(
                        'u.belong_type'   => 2,
                        'u.belong_time <='=> '2015-01-01'
                    )
                ),
                't.date_time between' => ['2015-01-01', '2015-01-31']
            ))
            ->left('user u on t.user_id = u.id')
            ->select('u.parent_id, count(1) as total')
            ->group('u.parent_id')
            ->findCustom(), 'parent_id', 'total');
        L('1月活跃技术员(去冻结)');
        //1月活跃技术员(去冻结)
        $aActiveCount = Arr::changeIndexToKVMap(DB()->get('tec_daily_combine_data t')
            ->where(array(
                't.is_active >'   => 0,
                'u.level'         => 4,
                'u.status'        => 0,
                array(
                    -1                => 'or',
                    'u.belong_type'   => 0,
                    array(
                        'u.belong_type'   => 2,
                        'u.belong_time <='=> '2015-01-01'
                    )
                ),
                't.date_time between' => ['2015-01-01', '2015-01-31']
            ))
            ->left('user u on t.user_id = u.id')
            ->select('u.parent_id, count(1) as total')
            ->group('u.parent_id')
            ->findCustom(), 'parent_id', 'total');
        L('1月份划转技术员个数(总共)');
        //1月划转技术员个数(总共)
        $aRebelongAll = Arr::changeIndexToKVMap(DB()->getUser()
            ->where(array(
                'level'         => 4,
                'belong_type'   => 2,
                'belong_time >' => '2015-01-01',
            ))
            ->select('parent_id, count(1) as total')
            ->group('parent_id')
            ->findCustom(), 'parent_id', 'total');
        L('1月划转技术员个数(去冻结)');
        //1月划转技术员个数(去冻结)
        $aRebelong = Arr::changeIndexToKVMap(DB()->getUser()
            ->where(array(
                'level'         => 4,
                'status'        => 0,
                'belong_type'   => 2,
                'belong_time >' => '2015-01-01',
            ))
            ->select('parent_id, count(1) as total')
            ->group('parent_id')
            ->findCustom(), 'parent_id', 'total');
        L('1月划转技术员中产生的有效技术员(去冻结)');
        //1月划转技术员中产生的有效技术员(去冻结)
        $aReEffectCount = Arr::changeIndexToKVMap(DB()->get('tec_daily_combine_data t')
            ->where(array(
                't.is_effect >'   => 0,
                'u.level'         => 4,
                'u.status'        => 0,
                'u.belong_type'   => 2,
                'u.belong_time >' => '2015-01-01',
                't.date_time between' => ['2015-01-01', '2015-01-31']
            ))
            ->left('user u on t.user_id = u.id')
            ->select('u.parent_id, count(1) as total')
            ->group('u.parent_id')
            ->findCustom(), 'parent_id', 'total');
        L('1月划转技术员中产生的活跃技术员(去冻结)');
        //1月划转技术员中产生的活跃技术员(去冻结)
        $aReActiveCount = Arr::changeIndexToKVMap(DB()->get('tec_daily_combine_data t')
            ->where(array(
                't.is_active >'   => 0,
                'u.level'         => 4,
                'u.status'        => 0,
                'u.belong_type'   => 2,
                'u.belong_time >' => '2015-01-01',
                't.date_time between' => ['2015-01-01', '2015-01-31']
            ))
            ->left('user u on t.user_id = u.id')
            ->select('u.parent_id, count(1) as total')
            ->group('u.parent_id')
            ->findCustom(), 'parent_id', 'total');

        while (
            $Channels=DB()->getUser('u')
            ->select('u.id, u.name, u1.name as zname')
            ->where(array('u.level' => '2'))
            ->left('user u1 on u.parent_id = u1.id')
            ->limit(($i++) * $iChunk, $iChunk)
            ->findMulti()
        ) {
            foreach ($Channels as $Channel) {
                $aData   = array();
                $aData[] = $Channel->zname;//大区
                $aData[] = $Channel->id;//渠道经理ID
                $aData[] = $Channel->name;//渠道经理姓名
                //1月有效技术员(去冻结),
                $aData[] = Arr::get($aEffectCount, $Channel->id, 0);
                //1月活跃技术员(去冻结),
                $aData[] = Arr::get($aActiveCount, $Channel->id, 0);
                //1月划转技术员个数(总共)
                $aData[] = Arr::get($aRebelongAll, $Channel->id, 0);
                //1月划转技术员个数(去冻结)
                $aData[] = Arr::get($aRebelong, $Channel->id, 0);
                //1月划转技术员中产生的有效技术员(去冻结)
                $aData[] = Arr::get($aReEffectCount, $Channel->id, 0);
                //1月划转技术员中产生的活跃技术员(去冻结)
                $aData[] = Arr::get($aReActiveCount, $Channel->id, 0);
                parent::sendFile('zhoulin.csv', implode(',', $aData));
            }
        }
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
            'u.level' => 4,
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
                $sAutoIP = $TecLoginLog->getAutoPC(Arr::get($aData, $Row->id, ''));
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
