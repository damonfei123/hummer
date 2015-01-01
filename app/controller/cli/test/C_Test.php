<?php
namespace App\controller\cli\test;

use App\system\controller\Cli_Base;
use Hummer\Component\Helper\Arr;


class C_Test extends Cli_Base {

    public function actionCache()
    {
        //$Cache = CTX()->CacheFile;
        //$Cache->store('user',array(1,3), 86400);
        //$Cache->delete('user');
        //$Cache->store('user',CTX()->Redis);//存储一天
        //var_export($Cache->fetch('user'));
    }

    public function actionData()
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
