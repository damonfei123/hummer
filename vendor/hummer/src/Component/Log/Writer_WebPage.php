<?php
/*************************************************************************************

   +-----------------------------------------------------------------------------+
   | Hummer [ Make Code Beauty And Web Easy ]                                    |
   +-----------------------------------------------------------------------------+
   | Copyright (c) 2014 https://github.com/damonfei123 All rights reserved.      |
   +-----------------------------------------------------------------------------+
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )                     |
   +-----------------------------------------------------------------------------+
   | Author: Damon <zhangyinfei313com@163.com>                                   |
   +-----------------------------------------------------------------------------+

**************************************************************************************/
namespace Hummer\Component\Log;

use Hummer\Component\Helper\Helper;
use Hummer\Component\Context\Context;

class Writer_WebPage implements IWriter{

    protected $aLog;
    protected $bEnable = true;

    public function __construct($sContentFormat=null)
    {
        $this->sContentFormat = Helper::TOOP(
            $sContentFormat === null,
            '[{sTime}] : {sContent}',
            $sContentFormat
        );
    }

    public function disable()
    {
        $this->bEnable = false;
    }

    public function enable()
    {
        $this->bEnable = true;
    }

    public function acceptData($aRow)
    {
        if ($this->bEnable) {
            $sLevelName = Logger::getLogNameByLevelID($aRow['iLevel']);
            $sLogMsg = str_replace(
                array('{sGUID}', '{iLevel}', '{sTime}', '{sContent}'),
                array($this->sGUID, $sLevelName, $aRow['sTime'], $aRow['sMessage']),
                $this->sContentFormat
            ) . PHP_EOL;

            #Add to queue
            $this->aLog[$aRow['iLevel'].'_'.$sLevelName][] = $sLogMsg;
        }
    }

    public function setGUID($sGUID)
    {
        #GUID should be same for one request
        $this->sGUID = $sGUID;
    }

    public function __destruct()
    {
        if (!$this->bEnable ||
            Context::getInst()->HttpRequest->isAjax() ||
            Context::getInst()->HttpRequest->getRequestMethod() !== 'GET'
        ) {
            return;
        }
        $sLight      = 'TPT_Info';
        $sLevelTitle = $sLevelMsg = '';

        krsort($this->aLog, SORT_NUMERIC);;

        foreach ($this->aLog as $sLevelName => $aLog) {
            $sLevelName = substr($sLevelName, strpos($sLevelName,'_') + 1 );
            $sItemErr   = 'TPT_Info';
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
    #Template_Log .TPT_light { width: 20px; height: 20px; float: right; cursor: pointer; border-radius: 15px; }
    #Template_Log .TPT_light.TPT_Err { background-color: red; -webkit-animation:mymove 0.5s infinite; }
    #Template_Log .TPT_light.TPT_Info { background-color: green;}
    #Template_Log .TPT_Msg{ width: 720px; background-color: #ECECEC; clear: right; float: right; padding-bottom: 10px;  font-family: 微软雅黑,幼圆; font-size: 14px; display: none; border: 1px solid #ccc; border-top: 0; box-shadow: 10px 10px 20px #ccc; border-bottom-left-radius: 10px; word-break: break-all; }
    #Template_Log .TPT_Msg .TPT_Err { background-color: #F00; }
    #Template_Log .TPT_Msg .TPT_Info { background-color: #C2BFBF; }
    #Template_Log ul { list-style-type: none; zoom: 1; overflow: hidden; border-bottom: 1px dashed #ccc; }
    #Template_Log ul, #Template_Log .TPT_Detail { margin: 0 0px; padding: 0; }
    #Template_Log ul li{ float: left; height: 30px; line-height: 30px; padding: 2px 15px; background-color: #000; color: #FFF; cursor: pointer; }
    #Template_Log .TPT_Detail { clear: both; padding: 0 10px; display: none; max-height: 550px; overflow-y: auto; overflow-x: hidden; }
    #Template_Log .TPT_Detail .TPT_Line { margin: 10px 0; border-bottom: 1px dashed #ccc; line-height: 30px; }

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
    <div class="TPT_light {$sLight}" id="TPT_light" onclick="_fm_log_show();">
    </div>
    <div class="TPT_Msg" id="TPT_Log_All_Msg" onclick="_fm_stop_propo();">
        <ul>
        {$sLevelTitle}
        </ul>
        {$sLevelMsg}
    </div>
</div>
<script type="text/javascript" charset="utf-8">

    //解决IE8之类不支持getElementsByClassName
    if (!document.getElementsByClassName) {
        document.getElementsByClassName = function (className, element) {
            var children = (element || document).getElementsByTagName('*');
            var elements = new Array();
            for (var i = 0; i < children.length; i++) {
                var child = children[i];
                var classNames = child.className.split(' ');
                for (var j = 0; j < classNames.length; j++) {
                    if (classNames[j] == className) {
                        elements.push(child);
                        break;
                    }
                }
            }
            return elements;
        };
    }

    var _fm_log_all_msg = '';
    var _fm_detail_msg  = '';

    window.onload = function(){
        _fm_log_all_msg = document.getElementById('TPT_Log_All_Msg');
        _fm_detail_msg  = document.getElementById('TPT_Detail_Msg');

        var body = document.getElementsByTagName('body')[0];
        body.onclick = function(){
            var TPT_light   = document.getElementById('TPT_light');
            var TPT_All_Msg = document.getElementById('TPT_Log_All_Msg');
            if (TPT_All_Msg.style.display == 'block') {
                TPT_light.click();
            }
        }
    }

    var _fm_show_ex = function(obj, level, evt){
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

        var e = (evt)?evt:window.event;
        if (window.event) {
            e.cancelBubble=true;// ie下阻止冒泡
        } else {
            e.stopPropagation();// 其它浏览器下阻止冒泡
        }
    }

    var _fm_log_show = function(evt){
        var block = _fm_log_all_msg;
        _fm_toggle_show(block);

        var e = (evt)?evt:window.event;
        if (window.event) {
            e.cancelBubble=true;// ie下阻止冒泡
        } else {
            e.stopPropagation();// 其它浏览器下阻止冒泡
        }
    }

    var _fm_stop_propo = function(evt){

        var e = (evt)?evt:window.event;
        if (window.event) {
            e.cancelBubble=true;// ie下阻止冒泡
        } else {
            e.stopPropagation();// 其它浏览器下阻止冒泡
        }
    }

    var _fm_toggle_show = function(obj){
        var block = obj.style.display;
        if(block == 'none' || block == ''){
            var _sf_all_debug = document.getElementById('TPT_Log_All_Msg');
            var _sf_all_debug_ul = _sf_all_debug.getElementsByTagName('ul')[0];
            var _sf_all_debug_li = _sf_all_debug_ul.getElementsByTagName('li')[0];
            _sf_all_debug_li.click();
            obj.style.display = 'block';
        }else{
            obj.style.display = 'none';
        }
    }
</script>
Log;
    }
}
