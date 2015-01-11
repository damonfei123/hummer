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
namespace Hummer\Component\Util\File;

use Hummer\Component\Context\Context;
use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class FileUpload{

    /**
     *  @var $_REQ HttpRequest
     **/
    protected $_REQ;

    /**
     *  @var $_aFileInfo upload file info
     **/
    protected $_aFileInfo;

    /**
     * @var $_sSavePath upload save file to dir
     **/
    protected $_sSavePath;

    /**
     * @var $_aConfig file upload config
     **/
    protected $_aConfig;

    public function __construct(
        $sFile,
        $sSavePath,
        $aConfig = array(),
        $aSaveFileName = array('\\Hummer\\Component\\Util\\File\\FileUpload', 'getFileName')
    ) {
        $this->_REQ           = Context::getInst()->HttpRequest;
        $this->_sSavePath     = Helper::TrimEnd($sSavePath,'/', 'r');
        $this->_aFileInfo     = $this->_REQ->getF($sFile);
        $this->aConfig        = array_merge(array(
            'ext' => '*',
            'max' => 2*1024*1024
        ), $this->parseExt($aConfig));
        $this->_aSaveFileName = $aSaveFileName;
    }

    /**
     *  Parse file config
     *  @param ext : jpg,png | image -> image ext,like jpg,png,bmp...
     **/
    protected function parseExt(array $aConfig = array())
    {
        $_aConfig = array();
        #Ext
        $sExt     = Arr::get($aConfig, 'ext', '');
        $aExt     = explode(',', $sExt);
        foreach ($aExt as &$ext) {
            if ($ext == 'image') {
                $ext = 'jpg,png,bmp,gif';
            }
        }
        $aConfig['ext'] = join(',', $aExt);
        #max
        $sMax   = Arr::get($aConfig, 'max', '');
        $sUnit  = strtoupper($sMax[strlen($sMax) - 1]);
        if ('M' == $sUnit) {
            $aConfig['max'] = (int)$sMax * 1024 * 1024;
        }elseif('K' == $sUnit){
            $aConfig['max'] = (int)$sMax * 1024;
        }
        return $aConfig;
    }

    /**
     *  file upload
     **/
    public function upload()
    {
        $iErrCode = 0;
        if (!$this->_aFileInfo) {
            $iErr = ERR_NOFILE;
            goto END;
        }
        if ($iErrCode=$this->checkFile()) {
            goto END;
        }
        $sFileFullName = $this->getFileFullName();
        if(!move_uploaded_file($this->_aFileInfo['tmp_name'], $this->getSaveFilePath())) {
            $iErrCode = self::ERR_FILE_UPLOAD_FAIL;
            goto END;
        }

        END:
        if (0 != $iErrCode) {
            $aRet = array(
                'iErrCode' => $iErrCode,
                'err_msg' => $this->getErrorMsg($iErrCode)
            );
        }else{
            $aRet = array(
                'iErrCode'  => 0,
                'filename'  => $sFileFullName,
                'filepath'  => $this->getSaveFilePath()
            );
        }
        return $aRet;
    }

    public static function getFileName($aFileInfo)
    {
        return md5_file($aFileInfo['tmp_name']);
    }
    public function getFileFullName()
    {
        return sprintf('%s.%s',
            call_user_func_array($this->_aSaveFileName, array($this->_aFileInfo)),
            $this->getFileExt()
        );
    }

    /**
     *  @var file pull path
     **/
    protected $_sTmpSaveFilePath = null;

    public function getSaveFilePath()
    {
        if (is_null($this->_sTmpSaveFilePath)) {
            $this->_sTmpSaveFilePath= sprintf('%s%s',
                $this->_sSavePath,
                $this->getFileFullName()
            );
        }
        return $this->_sTmpSaveFilePath;
    }

    /**
     *  @var file ext
     **/
    protected function getFileExt()
    {
        $sOriginName = $this->_aFileInfo['name'];
        return strtolower(substr($sOriginName, strpos($sOriginName, '.') + 1));
    }

    public function isAllowFile()
    {
        return $this->aConfig['ext'] == '*' ||
            in_array($this->getFileExt(), array_map('strtolower',explode(',',$this->aConfig['ext'])));
    }

    public function isBigFile()
    {
        return isset($this->aConfig['max']) && $this->_aFileInfo['size'] > $this->aConfig['max'];
    }

    public function checkHttpUpload()
    {
        $iErrCode = 0;
        switch ($this->_aFileInfo['error'])
        {
            case 1:
            case 2:
                $iErrCode = self::ERR_FILE_BIG;
                break;
            case 3:
                $iErrCode = self::ERR_HTTP_FILE_PARTY;
                break;
            case 4:
                $iErrCode = self::ERR_NOFILE;
                break;
        }
        return $iErrCode;
    }

    const ERR_NOFILE                = 10001;
    const ERR_SAVE_DIR_NOT_EXISTS   = 10002;
    const ERR_UPLOAD_WAY_ERR        = 10003;
    const ERR_FILE_NOT_ALLOWED      = 10004;
    const ERR_FILE_BIG              = 10005;
    const ERR_HTTP_FILE_PARTY       = 10006;
    const ERR_FILE_UPLOAD_FAIL      = 10007;
    const ERR_FILE_EXISTS           = 10008;

    public function getErrorMsg($iErrCode=null)
    {
        $aErrMsg = array(
            self::ERR_NOFILE            => '没有上传的文件',
            self::ERR_UPLOAD_WAY_ERR    => '非法文件上传',
            self::ERR_FILE_NOT_ALLOWED  => '文件上传格式非法',
            self::ERR_FILE_BIG          => '上传文件过大',
            self::ERR_HTTP_FILE_PARTY   => '只有部分文件上传',
            self::ERR_FILE_UPLOAD_FAIL  => '上传文件错误',
            self::ERR_FILE_EXISTS       => '文件已存在',
            self::ERR_SAVE_DIR_NOT_EXISTS => '上传目录不存在'
        );
        return Arr::get($aErrMsg, $iErrCode, '未知错误');
    }

    public function checkFile()
    {
        $iErrCode = 0;
        #save path not exists or unwritable
        if (!file_exists($this->_sSavePath) || !is_writeable($this->_sSavePath)) {
            $iErrCode = self::ERR_SAVE_DIR_NOT_EXISTS;
            goto END;
        }
        #file exists
        if (file_exists($this->getSaveFilePath())) {
            $iErrCode = self::ERR_FILE_EXISTS;
            goto END;
        }
        #Http error
        if ($iCheckCode = $this->checkHttpUpload()) {
            $iErrCode =  $iCheckCode;
            goto END;
        }
        #way
        if (!is_uploaded_file($this->_aFileInfo['tmp_name'])) {
            $iErrCode =  self::ERR_UPLOAD_WAY_ERR;
            goto END;
        }
        #type
        if (!$this->isAllowFile()) {
            $iErrCode = self::ERR_FILE_NOT_ALLOWED;
            goto END;
        }
        #size
        if ($this->isBigFile()) {
            $iErrCode = self::ERR_FILE_BIG;
            goto END;
        }
        END:
        return $iErrCode;
    }
}
