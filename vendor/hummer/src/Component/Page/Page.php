<?php
namespace Hummer\Component\Page;
/**
 * 分页类
*/
use Hummer\Component\Helper\Arr;

class Page {

    protected $HttpRequest;
    protected $iNumPerPage;
    protected $mDefaultRender;

    public function __construct(
        $HttpRequest ,
        $iNumPerPage=10,
        $mDefaultRender = array('Hummer\\Component\\Page\\Page', 'defaultRender')
    ) {
        $this->HttpRequest    = $HttpRequest;
        $this->iNumPerPage    = (int)$iNumPerPage;
        $this->mDefaultRender = $mDefaultRender;
    }

    public function getPage($M, &$aList)
    {
        return $this->getPageFromCB(
            array($M, 'findCount'),
            array($M, 'findMulti'),
            $aList
        );
    }

    public function getPageFromCB(
        $mCountCB,
        $mListCB,
        &$aList
    ) {
        $iPage       = max(1, (int)$this->HttpRequest->getG('page'));
        $iNumPerPage = max(1, $this->iNumPerPage);
        $sSelect     = $mCountCB[0]->getSelect();
        #get total
        $iTotal      = call_user_func($mCountCB);

        $iMaxPage    = ceil($iTotal / $this->iNumPerPage );

        if ($iPage > $iMaxPage) {
            throw new \InvalidArgumentException('[Page] : Page params error');
        }
        $M           = $mListCB[0];
        $M->select($sSelect)->limit(($iPage-1) * $iNumPerPage, $iNumPerPage);
        $aList       = call_user_func(array($M, $mListCB[1]));
        return call_user_func_array($this->mDefaultRender,array($this->HttpRequest,array(
            'page'      => $iPage,
            'total'     => $iTotal
        )));
    }

    public function defaultRender(
        $REQ,
        $aResult,
        array $aConfig = array(),
        $sDisplayItem = 'first.prev.list.next.last'
    ) {
        $iPage    =  Arr::get($aResult, 'page', 1);
        $iTotal   = Arr::get($aResult, 'total', 1);

        $iMaxPage = ceil($iTotal / $this->iNumPerPage );

        $aConfig = array_merge(array(
            'first' => '首页',
            'prev'  => '上一页',
            'next'  => '下一页',
            'last'  => '尾页'
        ), $aConfig);
        $aBindParam = array();
        $aParam     = explode('&',$REQ->getQueryString());
        if ($aParam) foreach ($aParam as $sParam) {
            $aItemParam = explode('=', $sParam);
            if ($aItemParam && count($aItemParam) == 2 && $aItemParam[0] != 'page'){
                $aBindParam[$aItemParam[0]] = $aItemParam[1];
            }
        }
        $sPagination  = '<div class=\'pagination\'>';
        $sBindParam   = http_build_query($aBindParam);
        $aDisplayItem = array();
        #首页
        $aDisplay['first'] = self::generateHtml(1, $sBindParam, $aConfig['first']);
        #上一页
        $aDisplay['prev']  = self::generateHtml( $iPage <=1 ? 1 : $iPage - 1, $sBindParam, $aConfig['prev']);
        #list
        $aDisplay['list']  = self::generateList($iPage, $iMaxPage, $sBindParam, $iPagePrev=3, $iPageNext=3);
        #下一页
        $aDisplay['next']  = self::generateHtml($iPage >= $iMaxPage ? $iMaxPage : $iPage + 1 , $sBindParam, $aConfig['next']);
        #尾页
        $aDisplay['last']  = self::generateHtml($iMaxPage, $sBindParam, $aConfig['last']);

        #return
        $sPagination  = '';
        $aDisplayItem = explode('.',$sDisplayItem);
        foreach ($aDisplayItem as $item) {
            if ($item && array_key_exists($item,$aDisplay)) {
                $sPagination .= $aDisplay[$item];
            }
        }

        return $sPagination;
    }

    public function generateList($iPage, $iMaxPage, $sBindParam, $iPagePrev, $iPageNext)
    {
        $sList = '';
        for ($i = 1; $i <= $iMaxPage; $i++) {
            if ($i == $iPage) {
                $sList .= '<span>'.$iPage.'</span>';
            }else{
                $sList .= self::generateHtml($i, $sBindParam, $i);
            }
        }
        return $sList;
    }
    public static function generateHtml($iPage, $sBindParam, $sItemName)
    {
        return sprintf("<a href='?page=%d&%s'>%s</a>",
            (int)$iPage,
            (string)$sBindParam,
            (string)$sItemName
        );
    }
}
