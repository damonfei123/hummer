<?php
namespace Hummer\Component\Log;

class Writer_WebPage implements IWriter{

    protected $aLog;
    protected $bEnable = true;

    public function __construct($sContentFormat=null)
    {
        $this->sContentFormat = $sContentFormat === null ? '[{sTime}] : {sContent}' : $sContentFormat;
    }

    public function acceptData($aRow)
    {
        if (!$this->bEnable) {
            return;
        }
        $sLevelName = Logger::getLogNameByLevelID($aRow['iLevel']);
        $sLogMsg = str_replace(
            array('{iLevel}', '{sTime}', '{sContent}'),
            array($sLevelName, $aRow['sTime'], $aRow['sMessage']),
            $this->sContentFormat
        ) . PHP_EOL;

        #Add to queue
        $this->aLog[$sLevelName][] = $sLogMsg;
    }

    public function setGUID($sGUID)
    {
        #GUID should be same for one request
        $this->sGUID = $sGUID;
    }

    public function __destruct()
    {
        if (!$this->bEnable) {
            return;
        }
        $sLight      = 'TPT_Info';
        $sLevelTitle = $sLevelMsg = '';

        foreach ($this->aLog as $sLevelName => $aLog) {
            $sItemErr = 'TPT_Info';
            if (in_array($sLevelName, array(
                Logger::DESC_WARN,
                Logger::DESC_NOTICE,
                Logger::DESC_ERROR,
                Logger::DESC_FATEAL
            ))) {
                $sLight   = 'TPT_Err';
                $sItemErr = 'TPT_Err';
            }

            #title
            $sLevelTitle .= ("<li class='TPT_Title ".$sItemErr."' onclick=\"_fm_show_ex(this,'".$sLevelName."')\">".$sLevelName."</li>");

            #detail
            $sLevelMsg .= "<div class=\"TPT_Detail\" id=\"TPT_Detail_Msg_".$sLevelName."\">";
            foreach ($aLog as $logDetail) {
                $sLevelMsg .= "<div class=\"TPT_Line\">".$logDetail."</div>";
            }
            $sLevelMsg .= "</div>";
        }

        echo <<<Log
<style type="text/css" media="screen">
    #Template_Log { position: fixed; top: 0; right: 0;}
    #Template_Log .TPT_light { width: 20px; height: 20px; float: right; cursor: pointer; }
    #Template_Log .TPT_light.TPT_Err { background-color: red; -webkit-animation:mymove 1s infinite; }
    #Template_Log .TPT_light.TPT_Info { background-color: #C2BFBF;}
    #Template_Log .TPT_Msg{ width: 600px; background-color: #ECECEC; clear: right; float: right; padding-bottom: 10px;  font-family: 微软雅黑,幼圆; font-size: 14px; display: none; }
    #Template_Log .TPT_Msg .TPT_Err { background-color: #F00; }
    #Template_Log .TPT_Msg .TPT_Info { background-color: #C2BFBF; }
    #Template_Log ul { list-style-type: none; zoom: 1; overflow: hidden; }
    #Template_Log ul, #Template_Log .TPT_Detail { margin: 0 0px; padding: 0; }
    #Template_Log ul li{ float: left; height: 30px; line-height: 30px; padding: 2px 15px; background-color: #000; color: #FFF; cursor: pointer; }
    #Template_Log .TPT_Detail { clear: both; padding: 0 10px; display: none; }
    #Template_Log .TPT_Detail .TPT_Line { margin: 10px 0; border-bottom: 1px dashed #ccc; }

    <!-- CSS3 -->
    @keyframes mymove
    {
        0% { background-color:#F00; }
        50% { background-color: rgb(255, 245, 0); }
        100% { background-color:#F00; }
    }

    @-moz-keyframes mymove /* Firefox */
    {
        0% { background-color:#F00; }
        50% { background-color: rgb(255, 245, 0); }
        100% { background-color:#F00; }
    }

    @-webkit-keyframes mymove /* Safari and Chrome */
    {
        0% { background-color:#F00; }
        50% { background-color: rgb(255, 245, 0); }
        100% { background-color:#F00; }
    }
</style>
<div id="Template_Log">
    <div class="TPT_light {$sLight}" onclick="_fm_log_show();">
    </div>
    <div class="TPT_Msg" id="TPT_Log_All_Msg">
        <ul>
        {$sLevelTitle}
        </ul>
        {$sLevelMsg}
    </div>
</div>
<script type="text/javascript" charset="utf-8">
    var _fm_log_all_msg = document.getElementById('TPT_Log_All_Msg');
    var _fm_detail_msg  = document.getElementById('TPT_Detail_Msg');

    var _fm_show_ex = function(obj, level){
        var TPT_Detail = document.getElementsByClassName('TPT_Detail');
        for(i=0; i< TPT_Detail.length; i++){
            TPT_Detail[i].style.display = 'none';
        }

        var TPT_Title = document.getElementsByClassName('TPT_Title');
        for(i=0; i< TPT_Title.length; i++){
            var bgc = TPT_Title[i].style.backgroundColor;
            TPT_Title[i].style.borderTop = '2px solid';
        }


        var _fm_show_detail = document.getElementById('TPT_Detail_Msg_'+level);
        _fm_show_detail.style.display = 'block';

        obj.style.borderTop = '2px solid blue';
    }

    var _fm_log_show    = function(){
        var block = _fm_log_all_msg;
        _fm_toggle_show(block);
    }

    var _fm_toggle_show = function(obj){
        var block = obj.style.display;
        if(block == 'none' || block == ''){
            document.getElementById('TPT_Log_All_Msg').childNodes[1].childNodes[1].click();
            obj.style.display = 'block';
        }else{
            obj.style.display = 'none';
        }
    }
</script>
Log;
    }
}
