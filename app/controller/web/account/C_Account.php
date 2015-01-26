<?php
namespace App\controller\web\account;

use App\system\controller\Web_Base;
use Hummer\Component\Util\Page\Page;

class C_Account extends Web_Base {

    public function __before__()
    {
        $this->Session = CTX()->Session;
    }

    public function actionFund(){
        $Session = CTX()->Session;
		if($Session::get('islogin')){
            go('/');
            return false;
        }
		$uid = $Session::get('userid');
		$u = DB()->getUser('', 'hf')->find($uid, true);
        $this->assign('u', $u);

		$nowPage = isset($_GET['page'])?$_GET['page']:1;

        $Page = new Page(10);
        $Page->setAssoc(true);
        $show = $Page->getPage( DB()->getInterest()
            ->order('time desc')
            ->where(array('uid' => $uid)), $list);

		foreach($list as $k => $v){
			$list[$k]['num'] = $k+1+($nowPage-1)*8;
			$list[$k]['time'] = date('Y-m-d H:i',$v['time']);
			$time1 = date('Ymd',$v['time']);
			$pid = $v['pid'];
			$p = DB()->getProject()->find($pid);
			$list[$k]['project'] = "<a href='__APP__/Project/detail/pid/$pid'>比特币质押标&nbsp;#$time1-".$pid."</a>";
			$list[$k]['amount'] = number_format($v['amount'],2);
			$list[$k]['status'] = ($v["status"]==0?'支出':'收入');
		}
        $this->assign(array(
            'interest'  => $list,
            'active'    => 'fund',
            'page'      => $show
        ));
    }
	public function actionProfile(){
        /*
		if(!$_SESSION['islogin']){$this->redirect('__APP__/User/login');}
		$id = $_SESSION['userid'];
		$u = M('user');
		$u = $u->find($id);
		$u['password'] = '******';
		$this->u = $u;
		$this->display();
        */
        $this->assign(array(
            'active' => 'profile'
        ));
    }
	public function change(){
		if(!$_SESSION['islogin']){$this->redirect('__APP__/User/login');}
		if(!$_POST){$this->redirect('__APP__/Index/index');}
		$name = array_keys($_POST);
		$name = $name[0];
		$value = $_POST[$name];
		$id = $_SESSION['userid'];
		$u = M('user');
		if($name=='phone'){
			$result = $u->where(array('id'=>$id))->setField($name,$value);
			$result = $u->where(array('id'=>$id))->setField('username',$value);
			$_SESSION['username'] = $value;
		}
		if($name=='password'){
			$result = $u->where(array('id'=>$id))->setField('password',md5($value));
		}
		if($result) {
			$this->ajaxReturn(1,'success',1);
		}else{
			$this->ajaxReturn(0,'error',0);
		}
	}
	public function actionInvest(){
        /*
		if(!$_SESSION['islogin']){
            $this->redirect('__APP__/User/login');
        }
        */
        $this->assign(array(
            'active'    => 'invest',
        ));
	}
	public function investhistory(){
		if(!$_SESSION['islogin']){$this->redirect('__APP__/User/login');}
		$i = M('invest');
		$uid = $_SESSION['userid'];
		$i = $i->where(array('uid'=>$uid))->order('time DESC')->select();
		$array = array();
		foreach($i as $k=>$v){
			$time=date("Y-m-d H:i",$v["time"]);
			$time1=date("Ymd",$v["time"]);
			$url = U('Project/detail?pid='.$v['pid']);
			$array[$k]=array(
				"num"=>$k+1,
				"time"=>$time,
				"pid"=>"<a href='$url'>比特币质押标&nbsp;#$time1-".$v["pid"]."</a>",
				"invest"=>'￥'.number_format($v["invest"],2),
			);
		}
		$this->ajaxReturn($array,'JSON');
	}
	public function actionBorrow(){
        /*
		if(!$_SESSION['islogin']){$this->redirect('__APP__/User/login');}
        */
        $this->assign(array(
            'active'    => 'borrow',
        ));
	}
	public function borrowhistory(){
		if(!$_SESSION['islogin']){$this->redirect('__APP__/User/login');}
		$p = M('project');
		$uid = $_SESSION['userid'];
		$p = $p->where(array('borrower'=>$uid))->order('aptime DESC')->select();
		$array = array();
		foreach($p as $k=>$v){
			$time=date("Y-m-d H:i",$v["aptime"]);
			$time1=date("Ymd",$v['aptime']);
			$url0 = U('Project/apply');
			$url = U('Project/detail?pid='.$v['id']);
			if($v["status"]==0){	$status="等待质押";	$pid="<a href='$url0'>比特币质押标&nbsp;#$time1-".$v["id"]."</a>";};
			if($v["status"]==1){	$status="正在融资";	$pid="<a href='$url'>比特币质押标&nbsp;#$time1-".$v["id"]."</a>";};
			if($v["status"]==2){	$status="正在还款";	$pid="<a href='$url'>比特币质押标&nbsp;#$time1-".$v["id"]."</a>";};
			if($v["status"]==3){	$status="项目结束";	$pid="<a href='$url'>比特币质押标&nbsp;#$time1-".$v["id"]."</a>";};
			if($v["status"]==4){	$status="项目失败";};
			$array[$k]=array(
				"num"=>$k+1,
				"time"=>$time,
				"pid"=>$pid,
				"amount"=>'￥'.number_format($v["amount"]),
				"status"=>$status,
			);
		}
		$this->ajaxReturn($array,'JSON');
	}


 }
?>
