<?php
namespace App\controller\cli\data;

use App\system\controller\Cli_Base;
use Hummer\Component\Helper\File;
use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class C_Data extends Cli_Base{

    public function __before__()
    {
        $this->memLimit();
    }

    public static function getFilePath($sFile)
    {
        return sprintf('%s%s','/home/zhangyinfei/project/test/data/nohup_id/', $sFile);
    }

    public function actionGuidDataDetail()
    {
        $aGUID  = File::getLineToArr(self::getFilePath('guid_lost.txt'));
        $aClean = array();
        foreach ($aGUID as $sGUID) {
            if (trim($sGUID)) {
                $aClean[trim($sGUID)] = 1;
            }
        }
        $aGUID  = array_keys($aClean);
        $i      = 0;
        $iTotal = count($aGUID);
        $aFreeze= self::freeze();
        $aTmpTT = array();
        foreach ($aGUID as $sGUID) {
            $this->Log->info(sprintf('已经开始处理第%d/%d个', $i++, $iTotal));
            $aTmpTT = Arr::changeIndexToKVMap(DB()->getActionInfo('','cheat')
                ->where(array('guid' => $sGUID))
                ->select('distinct tt')
                ->findCustom(),'tt','tt');
            foreach ($aTmpTT as $tt) {
                $sMsg = sprintf('%s,%d,%s', $sGUID, $tt, in_array($tt, $aFreeze) ? '冻结' : '解冻');
                File::send(self::getFilePath('user_info_detail.txt'), $sMsg."\r\n");
            }
        }
    }

    public function actionGuidDataTotal()
    {
        $aGUID  = File::getLineToArr(self::getFilePath('guid.txt'));
        foreach ($aGUID as $key => $sGUID) {
            $aGUID[$key] = trim($sGUID);
        }
        $aChunkGUID = array_chunk($aGUID, 1000);
        $i      = 0;
        $iTotal = count($aChunkGUID);
        $aFreeze= self::freeze();
        $aTmpTT = array();
        foreach ($aChunkGUID as $aG) {
            $this->Log->info(sprintf('已经开始处理第%d/%d个', $i++, $iTotal));
            $aTmpTT += Arr::changeIndexToKVMap(DB()->getActionInfo('','cheat')
                ->where(array('guid in' => $aG))
                ->select('distinct tt')
                ->findCustom(),'tt','tt');
        }
        $iFreeze = 0;
        foreach ($aTmpTT as $tt) {
            if (in_array($tt, $aFreeze)) {
                $iFreeze++;
            }
        }
        $sMsg = sprintf('冻结人数:%d,正常人数%d', $iFreeze, count($aTmpTT) - $iFreeze);
        File::send(self::getFilePath('guid_freeze_status_all'), $sMsg);
    }

    public function actionGuidData()
    {
        $aGUID  = File::getLineToArr(self::getFilePath('guid2.txt'));
        $aFreeze= self::freeze();
        //$aChunk = array_chunk($aGUID, 1000);
        $iTotal = count($aGUID);
        $i      = 0;
        foreach ($aGUID as $sCGUID) {
            $this->Log->info(sprintf('已经开始处理第%d/%d个', $i++, $iTotal));
            $sCGUID = trim($sCGUID);
            $aTmpTT = Arr::changeIndexToKVMap(DB()->getActionInfo('','cheat')
                ->where(array('guid' => $sCGUID))
                ->select('distinct tt')
                ->findCustom(),'tt','tt');
            $iFreeze = 0;
            $this->Log->info('查库完成');
            foreach ($aTmpTT as $tt) {
                //if (in_array($tt, $aFreeze)) {
                if (isset($aFreeze[$tt])) {
                    $iFreeze++;
                }
            }
            $aTmpRet = array();
            $aTmpRet['guid']    = $sCGUID;
            $aTmpRet['freeze']  = $iFreeze;
            $aTmpRet['normal']  = count($aTmpTT) - $iFreeze;
            File::send(self::getFilePath('guid_freeze_status2'), implode(',', $aTmpRet)."\r\n");
        }
    }

    public static function freeze()
    {
        return Arr::changeIndexToKVMap(DB()->getUser('', 'slave')
            ->where(array('status >' => 0))
            ->select('id')
            ->findCustom(),'id','id');
    }

    public function actionLiYan()
    {
        $this->memLimit();
        $iZoneID = 49653;
        $this->Log->info('取技术员');
        $aJSY    = self::getJiShuYuan();
        $aUserID = array_keys(Arr::changeIndex($aJSY, 'id'));
        #被限制自提数据
        $this->Log->info('取限制自提用户');
        $aDoubtCheat = array_keys(Arr::changeIndex(DB()->get('doubt_cheat', 'slave')
            ->where(array('kind' => 2, 'status' => 0, 'user_id in' => $aUserID))
            ->select('user_id')
            ->findCustom(), 'user_id'));
        #技术员余额
        $this->Log->info('取帐户余额');
        $aNiuBi = Arr::changeIndexToKVMap(DB()->get('user_point', 'youqian')
            ->where(array('user_id in' => $aUserID))
            ->select('user_id, point')
            ->findCustom(),'user_id', 'point');
        $aRet   = array();
        $iTotal = count($aJSY);
        $i      = 0;
        foreach ($aJSY as $aData) {
            $this->Log->info(sprintf('开始计算第%d/%d', $i++, $iTotal));
            $iUID        = $aData['id'];
            $aRet[$iUID] = array();
            $aRet[$iUID]['id']      = $iUID;
            $aRet[$iUID]['name']    = $aData['name'];
            $aRet[$iUID]['cname']   = $aData['cname'];
            $aRet[$iUID]['ct']      = $aData['create_time'];
            $aRet[$iUID]['freeze']  = $aData['status'] == 0 ? '否' : '是';
            $aRet[$iUID]['wangtui'] = $aData['promote_way'] == 256 ? '否' : '是';
            $aRet[$iUID]['doubt']   = Helper::TOOP(isset($aDoubtCheat[$iUID]), '是', '否');
            $aRet[$iUID]['point']   = isset($aNiuBi[$iUID]) ? $aNiuBi[$iUID] : 0;
            self::sendFile('liyan.csv', implode(',',$aRet[$iUID])."\r\n");
        }
    }

    public static function getJiShuYuan()
    {
        $Cache   = CTX()->CacheFile;
        $sKey = 'LIYAN_JISHUYUAN';
        #技术员信息
        if (!($aJSY=$Cache->fetch($sKey))) {
            $aJSY = DB()->getUser('u', 'slave')
                ->select('u.id,u.name,u.parent_id, u2.name as cname,u.create_time,u.status,u.promote_way')
                ->where(array('u.level' => 4, 'u.zone_user_id' => 49653))
                ->join('user u2 on u.parent_id = u2.id')
                ->join('user u3 on u2.parent_id = u3.id')
                ->findCustom();
        }
        return $aJSY;
    }
}
