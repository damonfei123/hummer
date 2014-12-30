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
namespace Hummer\Component\Util\Page;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;
use Hummer\Component\Context\Context;

class Page {

    /**
     *  @var $HttpRequest Context::HttpRequest
     **/
    protected $HttpRequest;

    /**
     *  @var $iNumPerPage Per num in every Page
     **/
    protected $iNumPerPage;

    /**
     *  @var $mDefaultRender array Render to show
     **/
    protected $mDefaultRender;

    /**
     *  @var $sPageStyle string page info style
     **/
    protected $sPageStyle;

    /**
     *  @var $aPageConfig array page config
     **/
    protected $aPageConfig;

    public function __construct(
        $iNumPerPage=10,
        $aPageConfig=array(),
        $sPageStyle=null,
        $mDefaultRender = array('Hummer\\Component\\Util\\Page\\Page', 'defaultRender')
    ) {
        $this->HttpRequest    = Context::getInst()->HttpRequest;
        $this->sPageStyle     = $sPageStyle;
        $this->aPageConfig    = (array)$aPageConfig;
        $this->iNumPerPage    = (int)$iNumPerPage;
        $this->mDefaultRender = $mDefaultRender;
    }

    public function getPage($M, &$aList)
    {
        $MM = clone $M;
        return $this->getPageFromCB(
            array($M, 'findCount'),
            array($MM, 'findMulti'),
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
        #get total
        $iTotal      = call_user_func($mCountCB);

        $iMaxPage    = ceil( $iTotal / $this->iNumPerPage );

        if ($iPage > $iMaxPage) {
            throw new \InvalidArgumentException('[Page] : Page params error, page > maxpage');
        }
        $M      = $mListCB[0];
        $M->limit(($iPage-1) * $iNumPerPage, $iNumPerPage);
        $aList  = call_user_func(array($M, $mListCB[1]));
        return call_user_func_array($this->mDefaultRender,array($this->HttpRequest,array(
            'page'      => $iPage,
            'total'     => $iTotal
        ),$this->aPageConfig, $this->sPageStyle));
    }

    public function defaultRender(
        $REQ,
        $aResult,
        array $aConfig = array(),
        $sDisplayItem  = null
    ) {
        $iPage    =  Arr::get($aResult, 'page', 1);
        $iTotal   = Arr::get($aResult, 'total', 1);
        $sDisplayItem = Helper::TOOP(
            $sDisplayItem,
            $sDisplayItem,
            'stat.pageInfo.first.prev.list.next.last'
        );

        $iMaxPage = ceil($iTotal / $this->iNumPerPage );

        $aConfig = array_merge(array(
            'stat'       => '总共%d条记录',
            'pageInfo'   => '第|page|页/共|total|页',
            'first'      => '首页',
            'prev'       => '上一页',
            'next'       => '下一页',
            'last'       => '尾页',
            'ellipsis'   => '...'
        ), $aConfig);

        $aBindParam = array();
        $aParam     = explode('&',$REQ->getQueryString());
        if ($aParam) foreach ($aParam as $sParam) {
            $aItemParam = explode('=', $sParam);
            if ($aItemParam && count($aItemParam) == 2 && $aItemParam[0] != 'page'){
                $aBindParam[$aItemParam[0]] = $aItemParam[1];
            }
        }
        #page stat
        $aDisplay['stat']     = sprintf('<span>'.$aConfig['stat'].'</span>',$iTotal);
        #pageInfo
        $aDisplay['pageInfo'] = strtr('<span>'.$aConfig['pageInfo'].'</span>',array(
            '|page|'  => $iPage,
            '|total|' => $iMaxPage
        ));

        $sBindParam   = http_build_query($aBindParam);
        $aDisplayItem = array();
        #first page
        $aDisplay['first'] = self::generateHtml(1, $sBindParam, $aConfig['first']);
        #prev page
        $aDisplay['prev']  = self::generateHtml(
            Helper::TOOP($iPage <=1, 1, $iPage-1),
            $sBindParam,
            $aConfig['prev']
        );
        #list
        $aDisplay['list']  = self::generateList(
            $iPage,
            $iMaxPage,
            $sBindParam,
            $iPagePrev=3,
            $iPageNext=4,
            $aConfig['ellipsis']
        );
        #next page
        $aDisplay['next']  = self::generateHtml(
            Helper::TOOP($iPage >= $iMaxPage,$iMaxPage,$iPage + 1) ,
            $sBindParam,
            $aConfig['next']
        );
        #last page
        $aDisplay['last']  = self::generateHtml($iMaxPage, $sBindParam, $aConfig['last']);

        #return
        $sPagination  = '';
        $aDisplayItem = explode('.',$sDisplayItem);
        foreach ($aDisplayItem as $item) {
            if ($item && array_key_exists($item,$aDisplay)) {
                $sPagination .= $aDisplay[$item];
            }
        }
        return sprintf('%s%s%s', "<div class='pagination'>", $sPagination, '</div>');
    }

    public function generateList(
        $iPage,
        $iMaxPage,
        $sBindParam,
        $iPagePrev,
        $iPageNext,
        $sEllipsis='...'
    ) {
        $sList = ''; $iTmpPage = $iPage;
        $iPageStart = Helper::TOOP(($iPage - $iPagePrev) > 0, ($iPage - $iPagePrev), 1);
        $iPageEnd   = Helper::TOOP(($iPage + $iPageNext) < $iMaxPage, ($iPage + $iPageNext), $iMaxPage);

        $sList .= Helper::TOOP($iPageStart != 1,self::generateHtml(1, $sBindParam, 1),'');
        $sList .= Helper::TOOP($iPageStart > 2 , sprintf('<span>%s</span>', $sEllipsis), '');

        #pre
        for ($i = $iPageStart; $i < $iPage; $i++) {
            if ($i == $iPage) {
                $sList .= '<span>'.$iPage.'</span>';
            }else{
                $sList .= self::generateHtml($i, $sBindParam, $i);
            }
        }
        #next
        for ($i = $iPage; $i <= $iPageEnd; $i++) {
            $sList .= self::generateHtml($i, $sBindParam, $i);
        }
        $sList .= Helper::TOOP($iPageEnd < $iMaxPage-1, sprintf('<span>%s<span>', $sEllipsis), '');
        $sList .= Helper::TOOP( $iPageEnd != $iMaxPage,
            self::generateHtml($iMaxPage, $sBindParam, $iMaxPage), '');

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
