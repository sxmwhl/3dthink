<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use User\Api\UserApi;
use Think\Controller;
/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController {

	/* 用户中心首页 */
	public function index(){
		
	}

	/* 注册页面 */
	public function register($username = '', $password = '', $repassword = '', $email = '', $verify = ''){
		$this->title="新用户注册";
		if(!C('USER_ALLOW_REGISTER')){
            $this->error('注册已关闭');
        }
       
		if(IS_POST){ //注册用户
			
			/* 检测验证码 */
			if(!$this->check_verify($verify)){
				$this->error('验证码输入错误！');
			}

			/* 检测密码 */
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}			
			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$uid = $User->register($username, $password, $email);
			
			if(0 < $uid){ //注册成功
				//TODO: 发送验证邮件
				
				$this->success('注册成功！',U('login'));
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}

		} else { //显示注册表单
			$this->display();
		}
	}

	/* 登录页面 */
	public function login($username = '', $password = '', $verify = ''){
		$this->title="登陆";
		if(IS_POST){ //登录验证			
			/* 检测验证码 */
			if(!$this->check_verify($verify)){
				$this->error('验证码输入错误！');
			}
			
			/* 调用UC登录接口登录 */
			$user = new UserApi;
			$uid = $user->login($username, $password);
			
			if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$Mb = D('Member');
				$ok=$Mb->login($uid);				
				if($ok){ //登录用户
					//TODO:跳转到登录前页面
					$this->success('登录成功！',U('Home/Index/index'));
				} else {
					$this->error("member 登陆出错");
					$this->error($Member->getError());
				}

			} else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}

		} else { //显示登录表单
			$this->display();
		}
	}

	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Member')->logout();
			$this->success('退出成功！', U('User/login'));
		} else {
			$this->redirect('User/login');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		ob_clean();
		$verify = new \Think\Verify();
		$verify->entry(1);
	}
	/*验证验证码*/
	function check_verify($code, $id = 1){
		ob_clean();
		$verify = new \Think\Verify();
		return $verify->check($code, $id);
	}
	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}


    /**
     * 用户中心
     * @author 石小明 <sxm@3dant.cn>
     */
    public function ucenter(){
    	$this->title="用户中心";
		if ( !is_login() ) {
			$this->error( '您还没有登陆',U('User/login') );
		}
        $uid = is_login();
        $Moxing=M('Moxing');
        $list=$Moxing->where('uid='.$uid)->select();
        $this->models=$list;
        $this->display();

    }
    public function diy(){
    	$this->title="模型组建";
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$uid = is_login();
    	$Diy=M('Diy');
    	$list=$Diy->where('uid='.$uid)->find();
    	if($list===NULL||$list===false){
    		$this->display('enableDiy');
    		exit();
    	}   
    	$Diy=M('Diy');
    	$list=$Diy->where('uid='.$uid)->find();
    	$this->diy=$list;
    	$this->display();
    
    }
    public function enableDiy(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$data=I('post.');
    	if($data['status']!==2)$data['status']=1;
    	$data['uid']=is_login();
    	$Diy=D('Diy');
    	if (!$Diy->create($data,1)){ // 创建数据对象
    		// 如果创建失败 表示验证没有通过 输出错误提示信息
    		exit($Diy->getError());
    	}else{
    		$id=$Diy->add();
    		//echo $Diy->getLastSql();
    		if(!$id) exit("开启我的家园失败！");
    	}
    	$this->success('开启我的家园成功！', 'diy');
    	//$this->display('diy');
    }
    public function saveDiy(){
    	$data=I('post.');
    	$data['shared']=htmlspecialchars_decode($data['shared']);
    	$data['shared']=strip_tags($data['shared'],'<transform><inline>');
    	$data['basic']=htmlspecialchars_decode($data['basic']);
    	$data['basic']=strip_tags($data['basic'],'<transform><shape><appearance><material><box><sphere><cone><cylinder><text>');
    	$Diy=D('Diy');
    	if (!$Diy->create($data,2)){ // 创建数据对象
    		// 如果创建失败 表示验证没有通过 输出错误提示信息
    		exit($Diy->getError());
    	}else{
    		$where="uid='".$data['uid']."'";
    		$id=$Diy->where($where)->save();
    		if($id==false) echo("添加错误状态");
    	}
    	echo json_encode($data);
    }
}
