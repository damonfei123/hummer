<?php
namespace App\controller\cli\test;

use App\system\controller\Cli_Base;
use Hummer\Component\Helper\File;
use Hummer\Component\Helper\Dir;
use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\HttpCall;


class C_Test extends Cli_Base {

    public function actionTest()
    {
        pr(HttpCall::callGET('http://damon.baidu.com:8925/index.php?xx=ee',
            array('xx' => 'damon'),
            array('headersD:damon')
        ));
        //$a = File::getFileToArr('/home/zhangyinfei/project/hummer/app/webroot/index.php');
        //$a = File::getCToArr('/home/zhangyinfei/project/hummer/app/webroot/index.php');
        //$a = Dir::showList('/home/zhangyinfei/project/hummer/app/webroot');
        //$a = Dir::showList('/home/zhangyinfei/project/hummer/app/webroot',true);
        //pr($a);
    }

    public function actionCache()
    {
        //$Cache = CTX()->CacheFile;
        //$Cache->store('arr', array('张银飞',2,'Damon飞'));
        //$Cache->store('arr', DB()->getUser()->findCustom(1));
        //pr($Cache->fetch('arr'));
        /*
        $aAllID   = unserialize(file_get_contents('id'));
        $aChunkID = array_chunk($aAllID, 1000);
        //找状态
        $aUserInfo = array();
        foreach ($aChunkID as $aUID) {
            $aChunkInfo = DB()->getUser('u', 'backup')
                ->select('id,name,status,channel_user_id,zone_user_id')
                ->where(array('id in' => $aUID))
                ->findCustom();
            $aUserInfo += Arr::changeIndex($aChunkInfo);
        }
        foreach ($aUserInfo as $UserInfo) {
            file_put_contents('idAllInfo.txt', implode(',', $UserInfo) . "\r\n", FILE_APPEND);
        }
        */
        //$Cache = CTX()->CacheFile;
        //$Cache->store('user',array(1,3), 86400);
        //$Cache->delete('user');
        //$Cache->store('user',CTX()->Redis);//默认存储一天
        //var_export($Cache->fetch('user'));
    }

    public function actionData()
    {
        $this->memLimit();
        $aAllIP = array_keys(Arr::changeIndex(self::getFile('ip2'), 'ip'));
        //找状态
        $aUserInfo = $aMInfo = $aBaseUserInfo = array();
        $iTotal    = count($aAllIP);
        $i         = 21685;
        $aAllIP    = array_slice($aAllIP, 21685);
        foreach ($aAllIP as $sIP) {
            $this->Log->info(sprintf('开始处理第%d/%d', $i++, $iTotal));
            $aTT = DB()->getActionInfo()->select('tt,type')->where(array('ip' => $sIP))->findCustom();
            //display by type
            $aTmpData = array();
            foreach ($aTT as $aT) {
                if (isset($aTmpData[$aT['type']])) {
                    $aTmpData[$aT['type']][] = $aT['tt'];
                }else{
                    $aTmpData[$aT['type']] = array($aT['tt']);
                }
            }
            $aIPStat = array();
            foreach ($aTmpData as $iType => $aTT) {
                $aIPStat['ip']   = $sIP;
                $aIPStat['type'] = $iType;
                //冻结tt号
                $aIPStat['freeze'] = self::getUserStatusStat($aTT, 1);
                //非冻结tt号
                $aIPStat['unfreeze'] = self::getUserStatusStat($aTT, 0);
                self::sendFile('id_ip.txt', implode(',', $aIPStat) . "\r\n");
            }
        }
    }

    public function actionDataGuid()
    {
        $this->memLimit();
        $Cache         = CTX()->CacheFile;
        $sCacheGuidKey = 'sDistinctGUID';
        //$Cache->delete($sCacheGuidKey);
        if (!($aAllGuid=$Cache->fetch($sCacheGuidKey))) {
            $aAllGuid = array_keys(Arr::changeIndex(DB()
                ->getActionInfo()
                ->select('distinct guid')
                ->where(array('tt in' => CFG()->get('id')))
                ->findCustom(),
            'guid'));
            $Cache->store($sCacheGuidKey, $aAllGuid, 86400 * 10);
        }
        $iTotal    = count($aAllGuid);
        $i         = 1;
        foreach ($aAllGuid as $sGUID) {
            $this->Log->info(sprintf('开始处理第%d/%d', $i++, $iTotal));
            $aTT = DB()->getActionInfo()
                ->select('tt,type,soft')
                ->where(array('guid' => $sGUID))
                ->findCustom();
            //display by type
            $aTmpData = array();
            foreach ($aTT as $aT) {
                $iSoft  = $aT['soft'];
                $iType  = $aT['type'];
                if (!isset($aTmpData[$iSoft])) {
                    $aTmpData[$iSoft] = array();
                }
                if (isset($aTmpData[$iSoft][$iType])) {
                    $aTmpData[$iSoft][$iType][] = $aT['tt'];
                }else{
                    $aTmpData[$iSoft][$iType] = array($aT['tt']);
                }
            }
            $aIPStat = array();
            foreach ($aTmpData as $iSoft => $aTT) {
                foreach ($aTT as $iType => $aT) {
                    $aIPStat['guid'] = $sGUID;
                    $aIPStat['soft'] = $iSoft;
                    $aIPStat['type'] = $iType;
                    //冻结tt号
                    $aIPStat['freeze'] = self::getUserStatusStat($aT, 1);
                    //非冻结tt号
                    $aIPStat['unfreeze'] = self::getUserStatusStat($aT, 0);
                    self::sendFile('id_guid.txt', implode(',', $aIPStat) . "\r\n");
                }
            }
        }
    }

    protected static function getUserStatusStat($aID, $iStatus=-1)
    {
        switch ($iStatus)
        {
            case 0://正常
                $aStatus = array('status' => 0);
                break;
            case 1://冻结
                $aStatus = array('status >' => 0);
                break;
            default://所有
                $aStatus = array();
                break;
        }
        if (0 == count($aID)) {
            return 0;
        }
        return DB()->getUser('','slave')
            ->where(array_merge(array('id in' => $aID), $aStatus))
            ->findCount();
    }

    public function actionData2()
    {
        ini_set('memory_limit', -1);
        $aID = CFG()->get('id');
        $this->Log->info('开始查IP..');
        $aIP = unserialize(file_get_contents('ip2'));
        if (!$aIP || count($aIP) == 0 ) {
            $aIP = DB()->getActionInfo()
                ->select('distinct ip')
                ->where(array('tt in' => $aID))
                ->findCustom();
            file_put_contents('ip2', serialize($aIP));
        }
        $this->Log->info('结束查IP..');

        $this->Log->info('开始查ID..');
        $aAllID = unserialize(file_get_contents('id'));
        if (!$aAllID || count($aAllID) == 0) {
            $aIP = array_keys(Arr::changeIndex($aIP, 'ip'));
            $aChunkIP = array_chunk($aIP, 2000);
            $aAllTT   = array();
            $iTotal   = count($aChunkIP);
            $i        = 1;
            foreach ($aChunkIP as $IP) {
                $this->Log->info(sprintf('开始%d/%d...', $i++, $iTotal));
                $aTT = DB()->getActionInfo()->where(array('ip in' => $IP))->select('tt')->findCustom();
                $aAllTT += Arr::changeIndex($aTT, 'tt');
            }
            $aAllID = array_keys($aAllTT);
            file_put_contents('id', serialize($aAllID));
        }
        $this->Log->info('结束查ID..');
        $aChunkID = array_chunk($aAllID, 1000);
        //找状态
        $aUserInfo = array();
        foreach ($aChunkID as $aUID) {
            $aChunkInfo = DB()->getUser()
                ->select('id,name,status')
                ->where(array('id in' => $aUID))
                ->findCustom();
            $aUserInfo += Arr::changeIndex($aChunkInfo);
        }
        foreach ($aUserInfo as $UserInfo) {
            file_put_contents('idInfo.txt', implode(',', $UserInfo) . "\r\n", FILE_APPEND);
        }
    }
}
