<?php
namespace App\controller\web\project;

use App\system\controller\Web_Base;
use Hummer\Component\Util\Page\Page;

class C_Project extends Web_Base {

    public function index(){
		$this->redirect('__APP__/Project/all');
		echo $t = mktime (19,10,21,6,26,2014)."<br>";
		echo date('Y-m-d',$t);
		//$this->display();
		$Invests = DB()->getInvest()->findCustom();
		foreach($Invests as $iK => $Invest){
			echo $Invest.'<br>';
		}
    }

	public function actionApply(){
        $Session = CTX()->Session;
		if($Session::get('islogin')) {
            go('/');
            return false;
        }
		$uid = $Session::get('userid');
		$p = DB()->getProject()->where(array('borrower'=>$uid,'status'=>0))->find(null, true);
		if($p['id']){
			$p['status'] = 0;
			$p['amount'] = round($p['amount']/10000,2);
		}else{
			$p['status'] = 1;
			$p['rate'] = 18;
		}
        $this->assign('p', $p);
	}

	public function actionAdd_POST(){
		if(!P('amount')){
            go('/main/index');
            return false;
        }
		$Project = M('project');

        $Session = CTX()->Session;

		//如果用户已经提交过未通过的申请，则无法添加
		$uid = $SESSION::get('userid');
		$p0 = DB()->getProject()->where(array('borrower'=>$uid,'status'=>0))->find(null, true);
		if($p0['id']){
			$this->ajaxReturn(1,'你的申请已经成功提交，请刷新申请页面',1);
			return false;
		}

		$p = P();
		$p['title']     = '比特币质押标';
		$p['aptime']    = time();
		$p['borrower']  = $Session::get('userid');
		$p['amount']    = 10000*P('amount');
		$p['rate1']     = $p['rate']-6;
		if(DB()->getProject()->add($p)){
			//添加一个比特币地址到新增项目
            /*
			$address = M('address');
			$a = $address->where(array('id'=>$result))->getField('address');
			$Project-> where(array('id'=>$result))->setField('address',$a);
			$data['status'] = 1;
			$data['info'] = '您的借款申请已提交成功';
            */
			$this->ajaxReturn($data,'JSON');
        }else{
            $this->error('写入错误！');
		}
	}

	public function actionUpdate_POST(){
		if(!P('amount')){
            go('/main/index');
            return false;
        }
		$p = P();
        unset($p['rpway'], $p['agreement']);
		$p['aptime'] = time();
		$p['amount'] = 10000 * P('amount');
		$p['rate1']  = $p['rate']-6;
        $Session     = CTX()->Session;
        $uid         = $Session::get('userid');
		if(DB()->getProject()->data($p)->update(array('borrower'=>$uid,'status'=>0))){
			echo $this->ajaxReturn(1, '您的借款申请已更新');
            //$this->success('操作成功！','apply');
        }else{
            $this->error('写入错误！');
		}
	}
	public function actionPaybtc(){
        $Session = CTX()->Session;
		if(!$Session::get('userid')){
            go('/main/index');
            return false;
        }
		$uid = $Session::get('userid');
		$address = DB()->getProject()
            ->where(array('borrower'=>$uid,'status'=>0))
            ->select('address')
            ->find()->address;
		$this->qrcode = '__APP__/Widget/qrCode/value/'.$address;
	}

    /**
     *  All Project
     **/
	public function actionAll(){
        $Page = new Page(10);
        $show = $Page->getPage(
            DB()->getProject()
                ->where(array('status in' => array(1,2,3)))
                ->order('status asc, rltime desc'),
            $aList);
        $this->assign('aList', $aList);
        $this->assign('page', $show);
    }

    /**
     *  Project Detail
     **/
	public function actionDetail(){
        $iPID = (int)G('pid');
        if (!$iPID) {
            go('/');
            return false;
        }

		$aProject = DB()->getProject()->find($iPID, true);
        if (!$aProject || count($aProject) == 0) {
            go('/');
            return false;
        }
		//相关数据处理
		$aProject['surplus'] = round($aProject['amount']-$aProject['invest'],2);
		$radio = 100*$aProject['invest']/$aProject['amount'];
		$aProject['radio']   = round($radio,2);
		$deadline = $aProject['deadline'];
		$aProject['deadtime'] = date('Y-m-d',$aProject['rltime'] + $deadline*24*60*60);
		$aProject['amount']   = $aProject['amount']/10000;
		$this->p = $aProject;
        $this->assign('p', $aProject);

		//得到用户数据
        $Session = CTX()->Session;
		$Session::set('userid',1);//TODO
		$iUID = $Session::get('userid');
		$U    = DB()->getUser('','hf')->find($iUID, true);
        $this->assign('u', $U);

		//计算还款计划
		$deadline = $aProject['deadline'];
		$aday     = 24*60*60;
		$rpdate   = $aProject['rpdate'];
		$start    = $aProject['rltime']+($deadline+1)*$aday;
		$period   = $aProject['period'];
		$rpnum    = $aProject['rpnum'];
		$amount   = $aProject['amount']*10000;
		$rate1    = $aProject['rate1']/1200;
		$interest = round($amount*$rate1,2);
		$end = $start+$period*31*$aday;
		$j = 0;
		$plan = array();
		for($i=$start;$i<$end;$i+=$aday){
			$day = date('d',$i);
			if($day==$rpdate){
				$plan[$j][0] = date('Y-m-d',$i);
				if($period>1){
					$plan[$j][1] = number_format($interest,2);
				}else{
					$plan[$j][1] = number_format($amount+$interest,2);
				}
				if($rpnum>0){
					$plan[$j][2] = "已还款";
				}else{
					$plan[$j][2] = "未到期";
				}
				$j++;$rpnum--;$period--;
			}
		}
		$this->plan = $plan;
        $this->assign('plan', $plan);

        $nowPage = G('page');
		//分布显示投资记录
        $Page = new Page(10);
        $show = $Page->getPage(
            DB()->getInvest()
                ->where(array('pid' => $iPID))
                ->order('time desc'),
            $aList);

        $aRetList = array();
		foreach($aList as $iK => $List){
            $aRetList[$iK]         = $List->getData();
			$aRetList[$iK]['num']  = $iK+1+($nowPage-1)*10;
			$aRetList[$iK]['time'] = date('Y-m-d H:i',$List->time);
			$u = DB()->getUser('','hf')->find($List->uid, true);
			$aRetList[$iK]['investor'] = substr_replace($u['username'],'****',3,4);
			$aRetList[$iK]['invest']   = number_format($List->invest,2);
		}

        $this->assign(array(
            'history' => $aRetList,
            'page'    => $show
        ));
	}
	public function invest(){
		if(!$_POST['invest']){$this->redirect('__APP__/Index/index');}
		$invest = M('invest');
		$i = $_POST;
		$i['time'] = time();
		$i['invest'] = round($_POST['invest'],2);
		$i['pid'] = $_POST['pid'];
		$i['uid'] = $_SESSION['userid'];
		$result1 = $invest->add($i);
		//更新项目数据
		$p = M('project');
		$result2 = $p->where(array('id'=>$i['pid']))->setInc('invest',$i['invest']);
		//更新用户数据
		$u = M("user"); // 实例化User对象
		$p = $p->find($i['pid']);
		$rate1 = $p['rate1'];
		$period = $p['period'];
		$income = $i['invest']*(1+$period*$rate1/1200);
		$income = round($income,2);
		$result3 = $u->where(array('id'=>$i['uid']))->setDec('free',$i['invest']);
		$result4 = $u->where(array('id'=>$i['uid']))->setInc('frozen',$income);
		if($result1&&$result2&&$result3&&$result4) {
            $this->ajaxReturn(1,'投资成功',1);
        }else{
            $this->ajaxReturn(0,'投资失败',0);
		}
	}
 }
?>
