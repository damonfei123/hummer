<?php
namespace App\controller\web\user;

use App\system\controller\Web_Base;

class C_User extends Web_Base{
    public function actionRegister(){
    }
	public function actionLogin(){
	}

    //注册用户
	public function add(){
		if($_POST["phone"]==''){	$this->redirect('__APP__/Index/index');};
		$User = D('User');
		if($User->create()) {
            $result =   $User->add();
            if($result) {
				$P = M('User');
				$P = $P->find($result);
				$_SESSION['userid'] = $P['id'];
				$_SESSION['username'] = $P['username'];
				$_SESSION['password'] = $P['password'];
				echo $_SESSION['islogin'] = true;
                //$this->success('操作成功！','read');
            }else{
                $this->error('写入错误！');
            }
        }else{
            $this->error($User->getError());
        }
	}

    /**
     *  登录入口
     **/
	public function loginCheck(){
		if($_POST['username']==''){	$this->redirect('__APP__/Index/index');};
		$username = $_POST['username'];
		$password = md5($_POST['password']);
		$User = M('User');
		$User = $User->where(array('username'=>$username,'password'=>$password))->find();
		if($User['id']){
			$_SESSION['userid'] = $User['id'];
			$_SESSION['username'] = $User['username'];
			$_SESSION['password'] = $User['password'];
			echo $_SESSION['islogin'] = true;
			$P = M('User');
			$data = array('lgip'=>get_client_ip(),'lgtime'=>time());
			$P->where(array('id'=>$User['id']))->setField($data);
		};
	}
    /**
     *  退出
     **/
	public function actionLogout(){
		$_SESSION = array('');
		session_destroy();
		$this->redirect('__APP__/Index/index');
	}
 }
?>
