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
		if ( !is_login() ) {
			$this->error( '您还没有登陆',U('User/login') );
		}
		$uid = is_login();
		$User=new UserApi();
		$info=$User->info($uid);
		$Member=M('Member');
		$list0=$Member->where('uid='.$uid)->find();
		$Diy=M('Diy');
		$list=$Diy->where('uid='.$uid)->select();
		$this->diy_num=count($list);
		$Moxing=M('Moxing');
		$list2=$Moxing->where('uid='.$uid)->select();
		$this->member=$list0;
		$Dns=M('Dns');
		$listDns=$Dns->where('uid='.$uid)->select();
		$Category=D('Category');
		$list3=$Category->get_child_categories(0);
		$this->categories=$list3;
		$this->user=$info;
		$this->diys=$list;
		$this->moxings=$list2;
		$this->dnses=$listDns;
		$this->title="用户中心";
		$this->display();
	}

	/* 注册页面 */
	public function register($username = '', $password = '', $repassword = '', $email = '', $verify = ''){
		
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
				$value=MD5($username.$uid."www.3Dant.cn");				
				$url="http://www.3dant.cn/index.php/Home/User/activate?id=".$uid."&code=".$value;
				$content="亲爱的<strong>".$username."</strong>:<br/>
						欢迎加入3d蚂蚁网！<br/>
						请点击下面的连接激活账号完成注册：<br/>
						<br/>
						<strong><a target='_blank' href='".$url."'>".$url."</a></strong><br/>
						<br/>
						<small>如果无法直接点击访问，请复制粘贴至浏览器地址栏访问。</small><br/>
						<br/>
						<a target='_blank' href='http://www.3dant.cn/'>3D蚂蚁</a>工作组 敬上<br/>
						<small>如果不是你本人操作，请忽略此邮件。此邮件自动生成，请勿回复。</small>";
				$res=think_send_mail($email,$username, '激活邮件！--3d蚂蚁www.3dant.cn',$content);
				if($res!==true)$this->error($res);				
				//TODO: 发送验证邮件
				$this->success('注册成功！','activate?u='.$username.'&e='.$email);			
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}
		} else { //显示注册表单
			$this->title="新用户注册";
			$this->display();
		}
	}
	
	public function activate(){
		$username=I('get.u/s','');
		$email=I('get.e/s','');
		if($username!="" && $email!=""){
			$this->flag=1;
			$this->title="账户激活提示";
			$this->email=$email;
			$this->username=$username;
			$this->display('activate');
			exit();
		}
		
		$wrong_message="激活参数错误，请确保完整复制粘贴邮件内激活地址！";
		$uid=I('get.id/d');
		$code=I('get.code/s');
		
		if(!$uid || !$code){
			$this->flag=0;
			$this->title="账户激活失败";
			$this->message=$wrong_message."（错误代码1）";
			$this->display();
			exit();
		}
		$User = new UserApi;
		$info=$User->info($uid);
		if(!is_array($info)){
			$this->flag=0;
			$this->title="账户激活失败";
			$this->message=$wrong_message."（错误代码2）";
			$this->display();
			exit();
		}
		if((int)$info['status']!=0){
			$this->flag=0;
			$this->title="已激活";
			$this->message="此帐号已激活过，请直接登录！";
			$this->display();
			exit();
		}
		$code2=MD5($info['username'].$uid."www.3Dant.cn");
		if ($code2!=$code2){
			$this->flag=0;
			$this->title="账户激活失败";
			$this->message=$wrong_message."（错误代码3）";
			$this->display();
			exit();
		}
		if(!$User->activate($uid)){
			$this->flag=0;
			$this->title="账户激活失败";
			$this->message=$wrong_message."（错误代码4）";
			$this->display();
			exit();
		}else{
			$this->success('账户激活成功！','login');
			exit();
		}
		$this->error('参数错误!','__ROOT__');	
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
					//设置cookie
					$uid = (string)$uid;
					$uname = (string)get_username();
					$expire = "3600";
					$key = "www3dantcn";
					$encode_data=array(
							'uid'=>$uid,
							'uname'=>$uname,
							'expire'=>$expire
					);
					setcookie('syncuyan', des_encrypt(json_encode($encode_data), $key), time() + 3600, '/', 'www.3dant.cn');
					//TODO:跳转到登录前页面
					$this->success('登录成功！',U('Home/User/index'));
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
			setcookie('syncuyan', 'logout', time() + 3600, '/', 'www.3dant.cn');
			$this->success('退出成功！', U('User/login'));
		} else {
			$this->redirect('User/login');
		}
	}
	/*修改会员信息*/
	public function modifyUserInfo(){
		echo("修改用户信息");
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
    public function diy(){    	
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$uid = is_login();
    	$id=I('get.id',0,'int');
    	$Diy=M('Diy');
    	$map['uid']=array('eq',$uid);
    	$map['status']=array('gt',0);
    	$list1=$Diy->where($map)->find();
    	if($list1===NULL||$list1===false){
    		$this->error('尚未开启DIY','/addDiy');
    	}
    	$map2['uid']=array('eq',$uid);
    	$map2['status']=array('gt',0);
    	$map2['id']=array('eq',$id);
    	$list=$Diy->where($map2)->find();
    	if($list===NULL||$list===false){
    		$this->error('该DIY不存在！');
    	}  	
    	$this->title="模型组建";
    	$this->diy=$list;
    	$this->display();
    
    }
    public function addDiy(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}    	
    	$uid=is_login();
    	$data=I('post.');
    	if(!$data){
    		$Diy=M('Diy');
    		$list=$Diy->where('uid='.$uid)->select();
    		if(count($list)>=8)$this->error('Diy数量已达上限');
    		$this->title="添加DIY";
    		$this->display();
    	}else {
    		if($data['status']!=2)$data['status']=1;
    		$data['uid']=is_login();
    		$Diy=D('Diy');
    		if (!$Diy->create($data,1)){ // 创建数据对象
    			// 如果创建失败 表示验证没有通过 输出错误提示信息
    			exit($Diy->getError());
    		}else{
    			$id=$Diy->add();
    			//echo $Diy->getLastSql();
    			if(!$id) exit("添加DIY失败！");
    		}
    		$oldname = __ROOT__."Public/images/preview.png";
    		$newPath = __ROOT__."Public/diy/".$data['uid']."/".$id."/";
    		create_dir($newPath) or exit("创建路径失败！");
    		$newname=$newPath."/preview.png";
    		copy($oldname, $newname);
    		$this->success('开启我的家园成功！', 'diy?id='.$id);
    	}    	
    }
    public function editDiy(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$uid=is_login();
    	$id=I('get.id',0,'int');
    	if($id===0)$this->error("参数错误！");
    	$Diy=D('Diy');
    	$result=$Diy->where('id='.$id)->find();
    	if($result['uid']!==$uid)$this->error('无权编辑此DIY！');
    	$data=I('post.');
    	if(!$data){
    		$this->diyinfo=$result;
    		$this->title="修改DIY基本信息";
    		$this->display();
    	}else {
    		if($data['status']!=2)$data['status']=1;
    		if(!$Diy->create($data,2)){
    			exit($Diy->getError());
    		}else {
    			$Diy->where('id='.$id)->save();
    			$this->success('编辑成功！','index');
    		}    		
    	}    	
    }
    public function deleteDiy(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$id=I('get.id',0,'int');
    	$uid=is_login();
    	$Diy=M('Diy');
    	$result1=$Diy->where('id='.$id)->find();
    	if($result1['uid']!==$uid)$this->error('无权删除此DIY！');
    	$result2=$Diy->where('id='.$id)->delete();
    	if($result2!==1)$this->error('删除错误!');
    	$dir=__ROOT__.'Public/diy/'.$uid.'/'.$id;
    	$result3 = deldir($dir);
    	if($result3===false)$this->error('数据删除，预览图片尚未删除！');
    	$this->success('删除成功！');   	
    }    
    public function saveDiy(){
    	if ( !is_login() ){
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$id=I('get.id',0,'int');
    	$input=I('post.');
    	$data['header']=htmlspecialchars_decode($input['header']);
    	$data['header']=trim(strip_tags($data['header'],'<viewpoint><navigationInfo><directionalLight><background><Environment>'));
    	$data['script']=htmlspecialchars_decode($input['script']);
    	$data['control']=htmlspecialchars_decode($input['control']);
    	$data['control']=trim(strip_tags($data['control'],'<button>'));
    	$data['route']=htmlspecialchars_decode($input['route']);
    	$data['route']=trim(strip_tags($data['route'],'<Route><TimeSensor><ColorInterpolator><CoordinateInterpolator><NormalInterpolator><OrientationInterpolator><PositionInterpolator><ScalarInterpolator><SplinePositionInterpolator><X3DInterpolatorNode>'));
    	$data['shared']=htmlspecialchars_decode($input['shared']);
    	$data['shared']=trim(strip_tags($data['shared'],'<transform><inline>'));
    	$data['basic']=htmlspecialchars_decode($input['basic']);
    	$data['basic']=trim(strip_tags($data['basic'],'<transform><shape><appearance><material><box><sphere><cone><cylinder><text><torus><plane>'));
    	$data['basic']=preg_replace( "@>(.*?)<@is", ">\n<", $data['basic'] );
    	$data['header']=preg_replace( "@>(.*?)<@is", ">\n<", $data['header'] );	
    	$data['route']=preg_replace( "@>(.*?)<@is", ">\n<", $data['route'] );
    	$data['time_update']=time();
    	$Diy=D('Diy');
    	if (!$Diy->create($data,2)){ // 创建数据对象
    		// 如果创建失败 表示验证没有通过 输出错误提示信息
    		$info['status']=false;
    		$info['message']=$Diy->getError();
    		exit(json_encode($info));
    	}else{
    		$map['id']=array('eq',$id);
    		$id=$Diy->where($map)->save();
    		if($id==false) {
    			$info['status']=false;
    			$info['message']="保存失败，请重试！";
    			exit(json_encode($info));
    		}else {
    			$info['status']=true;
    			$info['message']="保存成功！";
    		}    		
    	}
    	exit(json_encode($info));
    }
    public function diyPreview(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$id=I('get.id',0,'int');
    	$uid=is_login();
    	$Diy=M('Diy');
    	$result=$Diy->where("id=".$id)->find();
    	if($result['uid']!==$uid)$this->error("无权编辑此DIY");
    	$this->diy=$result;
    	$this->title="更换DIY《".$result['title']."》的缩略图";
    	$this->display();
    }
    public function diyPreviewSave(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$uid=is_login();
    	$id=I('get.id',0,'int');
    	$Diy=M('Diy');
    	$result=$Diy->where('id='.$id)->find();
    	if($uid!==$result['uid'])$this->error("无权编辑此DIY");
    	$upload = new \Think\Upload();// 实例化上传类
    	$upload->maxSize = 1048576 ;// 设置附件上传大小
    	$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    	$upload->rootPath = __ROOT__.'Public/'; // 设置附件上传根目录
    	$upload->autoSub  = false;
    	$upload->savePath = 'diy/'.$uid.'/'.$id.'/'; // 设置附件上传（子）目录
    	$upload->replace = true;
    	$upload->saveName = 'preview';
    	$upload->saveExt = 'png';
    	// 上传文件
    	$info = $upload->uploadOne($_FILES['preview']);
    	if(!$info) {// 上传错误提示错误信息
    		$this->error($upload->getError());
    	}else{// 上传成功 获取上传文件信息
    		$this->success('更换成功！','diy?id='.$id);
    	}
    }
    public function addDns(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}    	
    	$uid=is_login();
    	$data=I('post.');
    	if(!$data){
    		$Dns=M('Dns');
    		$list=$Dns->where('uid='.$uid)->select();
    		if(count($list)>=2)$this->error('您只能开启一个Dns网关');
    		$this->title="添加Dns网关";
    		$this->display();
    	}else {
    		if($data['status']!=1)$data['status']=0;
    		$data['uid']=is_login();
    		$data['ip']=ip2long($data['ip']);
    		$data['pw']=get_password(9);
    		$Dns=D('Dns');
    		if (!$Dns->create($data,1)){ // 创建数据对象
    			// 如果创建失败 表示验证没有通过 输出错误提示信息
    			exit($Dns->getError());
    		}else{
    			$id=$Dns->add();
    			//echo $Diy->getLastSql();
    			if(!$id) exit("添加DNS失败！");
    		}
    		$this->success('添加DNS成功！',U('User/index'));
    	}    	
    }
    public function editDns(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	$uid=is_login();
    	$id=I('get.id',0,'int');
    	if($id===0)$this->error("参数错误！");
    	$Dns=D('Dns');
    	$result=$Dns->where('id='.$id)->find();
    	if($result['uid']!==$uid)$this->error('无权编辑此Dns网关！');
    	$data=I('post.');
    	if(!$data){
    		$this->dnsinfo=$result;
    		$this->title="修改Dns网关";
    		$this->display();
    	}else {
    		if($data['status']!=1)$data['status']=0;
    		$data['ip']=ip2long($data['ip']);
    		if(!$Dns->create($data,2)){
    			exit($Dns->getError());
    		}else {
    			$Dns->where('id='.$id)->save();
    			$this->success('编辑成功！','index');
    		}
    	}
    }
    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile(){
    	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
    	if ( IS_POST ) {
    		//获取参数
    		$uid        =   is_login();
    		$password   =   I('post.old');
    		$repassword = I('post.repassword');
    		$data['password'] = I('post.password');
    		empty($password) && $this->error('请输入原密码');
    		empty($data['password']) && $this->error('请输入新密码');
    		empty($repassword) && $this->error('请输入确认密码');
    
    		if($data['password'] !== $repassword){
    			$this->error('您输入的新密码与确认密码不一致');
    		}
    
    		$Api = new UserApi();
    		$res = $Api->updateInfo($uid, $password, $data);
    		if($res['status']){
    			$this->success('修改密码成功！',U('User/index'));
    		}else{
    			$this->error($res['info']);
    		}
    	}else{
    		$this->title="修改密码";
    		$this->display();
    	}
    }
}
