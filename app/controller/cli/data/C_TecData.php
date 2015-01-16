<?php
namespace App\controller\cli\data;

use App\opt\TecDataHelper;
use App\system\controller\Cli_Base;
use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;
use Hummer\Component\Filesystem\File;

class C_TecData extends Cli_Base{

    public function __before__()
    {
        $this->memLimit();
    }

    public static function getFilePath($sFile)
    {
        $sDir = '/tmp/yinfei/';
        if (!File::Exists($sDir)) {
            Dir::makeDir($sDir);
        }
        return sprintf('%s%s',$sDir, $sFile);
    }

    /**
     *  3jk数据
     *  拉1个月数据，安装时间在00：00到6点之间有安装量，且这次guid在第二天，第三天，...第7天的启动
     *  日期2014.12
     **/
    public function actionGet3jk()
    {
        //冻结用户
        L('获取冻结技术员');
        $aFreeze = TecDataHelper::freeze();
        L('获取冻结技术员结束');

        $sStart = '2014-12-01'; $iStart = $iTmpStart = strtotime($sStart);
        $iEnd   = strtotime('+1 months',$iStart);
        $aSoft  = array(1,2, 6);
        foreach ($aSoft as $iSoftID) {
            $iStart = $iTmpStart;
            while ($iStart < $iEnd) {
                $aGUID = array();
                $sStart = date('Y-m-d', $iStart);
                //当天的安装
                $aInstall = TecDataHelper::getTimeRecord($sStart, $iSoftID);
                //接下来几天的日期
                $sNext2 = date('Y-m-d', strtotime('+1 day', $iStart));
                $sNext3 = date('Y-m-d', strtotime('+2 day', $iStart));
                $sNext4 = date('Y-m-d', strtotime('+3 day', $iStart));
                $sNext5 = date('Y-m-d', strtotime('+4 day', $iStart));
                $sNext6 = date('Y-m-d', strtotime('+5 day', $iStart));
                $sNext7 = date('Y-m-d', strtotime('+6 day', $iStart));

                foreach ($aInstall as $tt => $aInstallRow) {
                    $aGUID          = array_keys($aInstallRow);
                    $aRowList       = array();
                    $aRowList['tt'] = $tt;
                    $aRowList['date'] = $sStart;
                    $aRowList['状态'] = Helper::TOOP(in_array($tt,$aFreeze),'冻结','正常');
                    //当天的安装
                    $aRowList['安装量1'] = count($aGUID);
                    //第二天的安装
                    $aLate2 = TecDataHelper::getLateStart($tt, $sNext2, $iSoftID, $aGUID);
                    $aRowList['安装量2'] = count($aLate2);
                    //第三天的安装
                    $aLate3 = TecDataHelper::getLateStart($tt, $sNext3, $iSoftID, $aGUID);
                    $aRowList['安装量3'] = count($aLate3);
                    //第四天的安装
                    $aLate4 = TecDataHelper::getLateStart($tt, $sNext4, $iSoftID, $aGUID);
                    $aRowList['安装量4'] = count($aLate4);
                    //第五天的安装
                    $aLate5 = TecDataHelper::getLateStart($tt, $sNext5, $iSoftID, $aGUID);
                    $aRowList['安装量5'] = count($aLate5);
                    //第六天的安装
                    $aLate6 = TecDataHelper::getLateStart($tt, $sNext6, $iSoftID, $aGUID);
                    $aRowList['安装量6'] = count($aLate6);
                    //第七天的安装
                    $aLate7 = TecDataHelper::getLateStart($tt, $sNext7, $iSoftID, $aGUID);
                    $aRowList['安装量7'] = count($aLate7);

                    $sSeparator            = ' | ';
                    $aRowList['安装明细1'] = implode($sSeparator, $aGUID);
                    $aRowList['安装明细2'] = implode($sSeparator, $aLate2);
                    $aRowList['安装明细3'] = implode($sSeparator, $aLate3);
                    $aRowList['安装明细4'] = implode($sSeparator, $aLate4);
                    $aRowList['安装明细5'] = implode($sSeparator, $aLate5);
                    $aRowList['安装明细6'] = implode($sSeparator, $aLate6);
                    $aRowList['安装明细7'] = implode($sSeparator, $aLate7);

                    TecDataHelper::sendFile(
                        sprintf('get_install_late_data_%d.txt', $iSoftID),
                        implode(',', $aRowList).PHP_EOL
                    );
                }
                $iStart += 86400;
            }
        }
    }

    /**
     *  3jk数据
     *  拉3个月数据，在23点到6点有安装量
     *  日期2014-10,2014.11,2014.12
     **/
    public function actionGet3jkInstall()
    {
        $this->get3jkData(1);
    }

    /**
     *  3jk数据
     *  拉3个月数据，在23点到6点有启动的量
     *  日期2014-10,2014.11,2014.12
     **/
    public function actionGet3jkStart()
    {
        $this->get3jkData(3);
    }

    public function get3jkData($iType)
    {
        if (empty($iType)) {
            throw new \Exception('Error Type');
            return;
        }
        L('获取冻结技术员');
        $aFreeze = TecDataHelper::freeze();
        L('获取冻结技术员结束');
        $sStart = '2014-10-01'; $iStart = strtotime($sStart);
        $iEnd   = strtotime('+3 months',$iStart);
        //数据一天一天获取
        while ($iStart <= $iEnd) {
            $sStart = date('Y-m-d', $iStart);
            L(sprintf('开始计算%s的数据', $sStart));
            L('开始拉取记录');
            $i = 0;
            foreach($aTotal = TecDataHelper::getRecord($sStart, $iType) as $aTmpData){
                L(sprintf('落数据%d/%d', $i++, count($aTotal)));
                $sStatus    = Helper::TOOP(in_array($aTmpData['tt'], $aFreeze), '冻结', '正常');
                $aTmpData[] = $sStatus;
                TecDataHelper::sendFile(
                    sprintf('get_3jk_install_%d.txt', $iType),
                    implode(',', $aTmpData).PHP_EOL
                );
            }
            L(sprintf('结束计算%s的数据', $sStart));
            $iStart += 86400;
        }
    }

    public function actionUnInstall()
    {
        $Log     = CTX()->Log;
        $Log->info('开始计算');
        //冻结用户
        $Log->info('获取冻结技术员');
        $aFreeze = TecDataHelper::freeze();
        //计算日期
        $sStart  = '2014-11-01'; $iStart = strtotime($sStart);
        $sEnd    = '2014-11-30'; $iEnd   = strtotime($sEnd);
        //30天后数据
        $iLateStart = strtotime('+1 month',$iStart); $sLateStart = date('Y-m-d', $iLateStart);
        $iLateEnd   = strtotime('+1 month',$iEnd);   $sLateEnd   = date('Y-m-d', $iLateEnd);

        $Log->info('获取冻结技术员结束');
        //超时时间有安装数据
        $Log->info('开始获取技术员安装数据');
        $aInstall = TecDataHelper::getInstall($sStart, $sEnd);
        $Log->info('获取技术员安装数据完成');
        //30天后的数据
        $Log->info('开始获取技术员30天后的卸载数据');
        $aLateUnInstall = TecDataHelper::getLateUnInstall($sLateStart, $sLateEnd);
        $Log->info('开始获取技术员30天后的卸载数据完成');

        foreach (TecDataHelper::getSoft() as $iSoftID => $sSoftName) {
            $sStart  = '2014-11-01';
            $iStart = strtotime($sStart);
            do {
                $sStart = date('Y-m-d', $iStart);
                $Log->info('开始计算'.$sStart.'天数据');
                foreach ($aInstall as $key => $aRow) {
                    if (false !== strpos($key, sprintf('%s_%d_', $sStart, $iSoftID))) {
                        $sNextStart = date('Y-m-d', strtotime('+1 month', $iStart));
                        $aUnInstall = Arr::get(
                            $aLateUnInstall,
                            sprintf('%s_%d_%d', $sNextStart, $iSoftID, $aRow['user_id']),
                            array()
                        );
                        $aData = array(
                            'date'  => $sStart,
                            'tt'    => $aRow['user_id'],
                            'effect_org' => $aRow['effect_org'],
                            'lateUnInstall' => Arr::get($aUnInstall, 'daily_uninstall', 0),
                            'status'     => in_array($aRow['user_id'], $aFreeze) ? '冻结' : '正常'
                        );
                        TecDataHelper::sendFile('file_'.$iSoftID, implode(',', $aData).PHP_EOL);
                    }
                }
                $iStart += 86400;//加一天
            } while ($iStart <= $iEnd);
        }
    }
}
