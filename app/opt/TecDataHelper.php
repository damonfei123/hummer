<?php
namespace App\helper;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Filesystem\File;
use Hummer\Component\Filesystem\Dir;

class TecDataHelper{

    /**
     *  计算软件
     **/
    public static function getSoft()
    {
        return array(
            1 => '百度卫士',
            2 => '百度杀毒',
            6 => '百度浏览器'
        );
    }

    /**
     *  获取记录
     **/
    public static function getRecord($sDate, $iType)
    {
        return DB()->getActionInfo()->where(array(
            'date'  => $sDate,
            'type'  => $iType,
            array(
                -1 => 'or',
                'happen_time > ' => strtotime($sDate . ' 23:00:00'),
                'happen_time < ' => strtotime($sDate . ' 06:00:00')
                )
        ))->select(
                'soft,tt,guid,ip,isp,country,province,
                city,county,from_unixtime(happen_time) as happen_time'
        )->findCustom();
    }

    /**
     *  获取安装记录
     **/
    public static function getTimeRecord($sDate, $iSoftID)
    {
        $aRows = DB()->getActionInfo()->where(array(
            'date'  => $sDate,
            'soft'  => $iSoftID,
            'type'  => 1,
            'happen_time < ' => strtotime($sDate . ' 06:00:00'),
        ))->select('tt, guid')->findCustom();
        $aRet = array();
        foreach ($aRows as $aRow) {
            $tt   = $aRow['tt'];
            $guid = $aRow['guid'];
            $aRet[$tt][$guid] = true;
        }
        return $aRet;
    }

    /**
     *  获取用户第N天后是否还有启动
     **/
    public static function getLateStart($tt, $sDate, $iSoftID, $aGUID)
    {
        if (count($aGUID) == 0 || !$iSoftID) {
            return array();
        }
        return array_keys(Arr::changeIndex(DB()->getActionInfo()->where(array(
            'date'  => $sDate,
            'guid in' => $aGUID,
            'soft'  => $iSoftID,
            'type'  => 3
        ))->select('distinct guid')->findCustom(), 'guid'));
    }

    /**
     *  冻结的用户
     **/
    public static function freeze()
    {
        $Cache = CTX()->CacheFile;
        $sKey  = 'Freeze_User';
        $aFreeze =$Cache->fetch($sKey);
        if (empty($aFreeze)) {
            $aFreeze = Arr::changeIndexToKVMap(DB()->getUser('', 'slave')
                ->where(array('status >' => 0, 'level' => 4))
                ->select('id')
                ->findCustom(),'id','id');
            $Cache->store($sKey, $aFreeze);
        }
        return $aFreeze;
    }

    public static function getInstall($sStart, $sEnd)
    {
        $Cache = CTX()->CacheFile;
        $sKey  = 'TecDataHelper_getInstall';
        $aRet  = $Cache->fetch($sKey);
        if (empty($aRet)) {
            $aInstall = DB()->get('tec_daily_data', 'slave')
                ->where(array(
                    'soft_id in'   => array_keys(self::getSoft()),
                    'date between' => array($sStart, $sEnd),
                    'effect_org >' => 0
                ))
                ->select('user_id, soft_id, date, effect_org')
                ->findCustom();
            $aRet = array();
            foreach ($aInstall as $aRow) {
                $sTmpKey = sprintf('%s_%d_%d', $aRow['date'], $aRow['soft_id'], $aRow['user_id']);
                $aRet[$sTmpKey] = $aRow;
            }
            $Cache->store($sKey, $aRet);
        }
        return $aRet;
    }

    public static function getLateUnInstall($sStart, $sEnd)
    {
        $Cache = CTX()->CacheFile;
        $sKey  = 'TecDataHelper_getLateUnInstall';
        $aRet  = $Cache->fetch($sKey);
        if (empty($aRet)) {
            $aUnInstall = DB()->get('tec_daily_data', 'slave')
                ->where(array(
                    'soft_id in'   => array_keys(self::getSoft()),
                    'date between' => array($sStart, $sEnd)
                ))
                ->select('user_id, soft_id, date, daily_uninstall')
                ->findCustom();
            $aRet = array();
            foreach ($aUnInstall as $aRow) {
                $sTmpKey   = sprintf('%s_%d_%d', $aRow['date'], $aRow['soft_id'], $aRow['user_id']);
                $aRet[$sTmpKey] = $aRow;
            }
            $Cache->store($sKey, $aRet);
        }
        return $aRet;
    }

    public static function sendFile($sFile, $sContent, $bAppend=true)
    {
        $sDir = '/tmp/yinfei/';
        if (!File::Exists($sDir) && !Dir::makeDir($sDir)) {
            throw new \Runtimeexception('[TecDataHelper] : Save Dir Not exits');
        }
        file_put_contents(
            '/tmp/yinfei/'.$sFile,
            $sContent,
            $bAppend ? FILE_APPEND : ''
        );
    }

}
