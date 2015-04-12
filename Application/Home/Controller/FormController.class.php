<?php
namespace Home\Controller;
use User\Api\UserApi;
use Think\Controller;
class FormController extends Controller {
	public function _before_index(){
	if ( !is_login() ) {
    		$this->error( '您还没有登陆',U('User/login') );
    	}
	}
    public function index(){    	
      $this->title='分型您的模型';
      $this->display();
    }
    public function upload(){
    	$uid=is_login();
    	$upload = new \Think\Upload();// 实例化上传类
    	$upload->maxSize = 3145728 ;// 设置附件上传大小
    	$upload->exts = array('x3d');// 设置附件上传类型   
    	//$upload->mimes = array('application/xml','text/html','text/xml','model/x3d+xml');
    	$upload->rootPath = __ROOT__.'Public/'; // 设置附件上传根目录
    	$upload->autoSub  = false;
    	$upload->savePath = 'upload/'; // 设置附件上传（子）目录
    	// 上传文件
    	$info = $upload->uploadOne($_FILES['model']);
    	//exit($info['type']);
    	if(!$info) {// 上传错误提示错误信息
    		$this->error($upload->getError());
    	}else{// 上传成功 获取上传文件信息
    		$savePath=__ROOT__.'Public/'.$info['savepath'];
    		$movePath=$savePath.$info['md5'].'/';
    		$Moxing_check=M('Moxing');
    		$where_md5="folder='".$info['md5']."'";
    		$result_check=$Moxing_check->where($where_md5)->find();
    		if(!empty($result_check)){
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error('模型已上传过！',__MODULE__.'/index/model?f='.$info['md5'],3);  			
    		}    		
    		if(!file_exists($movePath)){
    		mkdir($movePath) or exit('创建路径失败，请重试！');
    		}
    		if(!check_x3d_document($savePath.$info['savename'], $movePath.'model.x3d')){
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error('模型文件有误！');
    		}
    		$copy_preview_ok=copy(__ROOT__.'Public/images/preview.png',$movePath.'preview.png');
    		mkdir($movePath.'texture/') or exit('创建路径失败，请重试！');
    		$Moxing=D('Moxing');
    		$inputs['folder']=$info['md5'];
    		$inputs['time_update']=date('Y-m-d H:i:s',time());  		
    		$inputs['ip_upload']=get_client_ip();
    		$inputs['uid']=$uid;
    		$User=new UserApi();
    		$user_info=$User->info($uid);
    		$inputs['creator']=$user_info[1];
    		$inputs['email']=$user_info[2];
    		if (!$Moxing->create($inputs)){ // 创建数据对象
    			// 如果创建失败 表示验证没有通过 输出错误提示信息
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error($Moxing->getError());    			 
    		}else{
    			// 验证通过 写入新增数据
    			$rename_model_ok=rename($savePath.$info['savename'], $movePath."model.x3d");
    			if(!$rename_model_ok)$this->error('模型移动失败！');
    			$result_add=$Moxing->add();    			
    			if(!$result_add)$this->error('模型分享失败！');     				 
    		}
    	}
    	
    	$this->success('模型分享成功','modify?f='.$info['md5']);
    }
    public function modify(){   
    	$md5=I('get.f/s');
    	$cate=I('cate');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$Category=D('Category');
    	$category_option = $Category->get_category_option(0,$cate,0);
    	$this->category_option=$category_option;
    	$Moxing=M('Moxing');
    	$where="folder='".$md5."'";
    	$model_show=$Moxing->where($where)->find();
    	$array1=explode(',',$model_show['vp_position']);
    	$model_show['vp_x']=$array1[0];
    	$model_show['vp_y']=$array1[1];
    	$model_show['vp_z']=$array1[2];
    	$array2=explode(',',$model_show['vp_orientation']);
    	$model_show['vp_d_x']=$array2[0];
    	$model_show['vp_d_y']=$array2[1];
    	$model_show['vp_d_z']=$array2[2];
    	$model_show['vp_d_a']=$array2[3];
    	$model_show['hl_on_checked']=$model_show['hl_on']==0?"":"checked='checked'";
    	$model_show['dl_on_checked']=$model_show['dl_on']==0?"":"checked='checked'";
    	$model_show['hl_on']=$model_show['hl_on']==0?"false":"true";
    	$model_show['dl_on']=$model_show['dl_on']==0?"false":"true";
    	$model_show['ip_last_modify']=get_client_ip();
    	$model_show['old_category']=$cate;
    	$this->model=$model_show;
    	$this->title='修改模型'.$model_show['title'].'的信息';
    	$this->display('modify');
    }
    public function save(){
    	$inputs=I('post.');
    	$inputs['vp_position']=$inputs['vp_x'].','.$inputs['vp_y'].','.$inputs['vp_z'];
    	$inputs['vp_orientation']=$inputs['vp_d_x'].','.$inputs['vp_d_y'].','.$inputs['vp_d_z'].','.$inputs['vp_d_a'];
    	$inputs['time_update']=date('Y-m-d H:i:s',time());
    	$inputs['dl_on']=empty($inputs['dl_on'])?0:1;
    	$inputs['hl_on']=empty($inputs['hl_on'])?0:1;
    	$Moxing=D("Moxing");
    	if (!$Moxing->create($inputs,2)){ // 创建数据对象
    		// 如果创建失败 表示验证没有通过 输出错误提示信息
    		exit($Moxing->getError());
    	}else{
    		// 验证通过 写入新增数据
    		//echo $Moxing->title;
    		$where="folder='".$inputs['folder']."'";
    		$Moxing->where($where)->save();
    		//更新分类下模型数
    		if($inputs['category']!=$inputs['old_category']){
    			$Category=D('Category');
    			if($inputs['category']!=0){
    				$Category->add_postcount($inputs['category']);
    			}
    			if($inputs['old_category']!=0){
    				$Category->del_postcount($inputs['old_category']);
    			}
    		}
    	}
    	//echo $Moxing->getLastSql(); //最后运行sql语句
    	header("Location:".__MODULE__."/index/model?f=".$inputs['folder']);
    	//$this->display('public:jump');
    }
    public function preview(){
    	$md5=I('f');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$Moxing=M('Moxing');
    	$where="folder='".$md5."'";
    	$model_show=$Moxing->where($where)->find();
    	$model_show['hl_on']=$model_show['hl_on']==0?"false":"true";
    	$model_show['dl_on']=$model_show['dl_on']==0?"false":"true";
    	$Category=D('Category');
    	$list3=$Category->get_child_categories(0);
    	$this->categories=$list3;
    	$this->model=$model_show;
    	$this->title="更换模型《".$model_show['title']."》的缩略图";
    	$this->display('preview');
    }
    public function texture(){
    	$md5=I('get.t/s');
    	$type=I('get.n/s');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$Moxing=M('Moxing');
    	$where="folder='".$md5."'";
    	$model_show=$Moxing->where($where)->find();
    	$model_show['hl_on']=$model_show['hl_on']==0?"false":"true";
    	$model_show['dl_on']=$model_show['dl_on']==0?"false":"true";
    	$this->model=$model_show;
    	$texture_list=scandir(__ROOT__.'Public/upload/'.$md5.'/texture/');
    	$Category=D('Category');
    	$list3=$Category->get_child_categories(0);
    	$this->categories=$list3;
    	$this->textureList=$texture_list;
    	$this->title="上传模型《".$model_show['title']."》的贴图";
    	switch ($type){
    		case "image":
    			$this->display('texture');
    			break;
    		case "movie":
    			$this->display('movie');
    			break;
    		case "audio":
    			$this->display('audio');
    			break;
    		default:
    			$this->display('texture');
    	}    	
    }
    public function textureSave(){
    	$md5=I('get.t/s');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$texture_exist=I('post.texture_exist/s');
    	$texture_num=I('post.texture_num/d');
    	$upload = new \Think\Upload();// 实例化上传类
    	$upload->maxSize = 3145728 ;// 设置附件上传大小
    	$upload->exts = array('jpg','gif','png','jpeg');// 设置附件上传类型
    	$upload->rootPath = __ROOT__.'Public/'; // 设置附件上传根目录
    	$upload->autoSub  = false;
    	$upload->savePath = 'upload/'; // 设置附件上传（子）目录
    	// 上传文件
    	$info = $upload->uploadOne($_FILES['texture']);
    	if(!$info) {// 上传错误提示错误信息
    		$this->error($upload->getError());
    	}else{
    		$savePath=__ROOT__."Public/upload/";
    		$movePath=$savePath.$md5."/texture/";
    		if(!strstr($texture_exist,$info['name'])){
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error('上传文件名与选择的不一致','texture?t='.$md5 ,'5');
    		}
    		$texture_exist_list=scandir(__ROOT__.'Public/upload/'.$md5.'/texture/');
    		$texture_exist_num=count($texture_exist_list)-2;
    		if($texture_exist_num>=$texture_num||$texture_exist_num>=20){
    			if(!in_array($info['name'], $texture_exist_list)){
    				$result_delete = @unlink ($savePath.$info['savename']);
    				$this->error('上传的贴图文件数量过多！','texture?t='.$md5 ,'5');
    			}
    		}
    		if(!file_exists($movePath)){
    			mkdir($movePath) or exit('创建路径失败，请重试！');
    		}
    		$rename_model_ok=rename($savePath.$info['savename'], $movePath.$info['name']);
    		if(!$rename_model_ok){
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error('上传贴图文件失败，请重试！','texture?t='.$md5 ,'5');
    		}
    	}
    	$this->success('上传贴图成功','texture?t='.$md5);
    }
    public function movieSave(){
    	$md5=I('get.t/s');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$texture_exist=I('post.texture_exist/s');
    	$texture_num=I('post.texture_num/d');
    	$upload = new \Think\Upload();// 实例化上传类
    	$upload->maxSize = 3145728;// 设置附件上传大小
    	$upload->exts = array('mp4','ogv');// 设置附件上传类型
    	$upload->rootPath = __ROOT__.'Public/'; // 设置附件上传根目录
    	$upload->autoSub  = false;
    	$upload->savePath = 'upload/'; // 设置附件上传（子）目录
    	// 上传文件
    	$info = $upload->uploadOne($_FILES['texture']);
    	if(!$info) {// 上传错误提示错误信息
    		$this->error($upload->getError());
    	}else{
    		$savePath=__ROOT__."Public/upload/";
    		$movePath=$savePath.$md5."/texture/";
    		if(!strstr($texture_exist,$info['name'])){
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error('上传文件名与选择的不一致','texture?t='.$md5 ,'5');
    		}
    		$texture_exist_list=scandir(__ROOT__.'Public/upload/'.$md5.'/texture/');
    		$texture_exist_num=count($texture_exist_list)-2;
    		if($texture_exist_num>=$texture_num||$texture_exist_num>=20){
    			if(!in_array($info['name'], $texture_exist_list)){
    				$result_delete = @unlink ($savePath.$info['savename']);
    				$this->error('上传的视频文件数量过多！','texture?t='.$md5 ,'5');
    			}
    		}
    		if(!file_exists($movePath)){
    			mkdir($movePath) or exit('创建路径失败，请重试！');
    		}
    		$rename_model_ok=rename($savePath.$info['savename'], $movePath.$info['name']);
    		if(!$rename_model_ok){
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error('上传视频文件失败，请重试！','texture?t='.$md5 ,'5');
    		}
    	}
    	$this->success('上传视频文件成功','texture?t='.$md5);
    }
    public function audioSave(){
    	$md5=I('get.t/s');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$texture_exist=I('post.texture_exist/s');
    	$texture_num=I('post.texture_num/d');
    	$upload = new \Think\Upload();// 实例化上传类
    	$upload->maxSize = 3145728;// 设置附件上传大小
    	$upload->exts = array('wav','mp3','ogg');// 设置附件上传类型
    	$upload->rootPath = __ROOT__.'Public/'; // 设置附件上传根目录
    	$upload->autoSub  = false;
    	$upload->savePath = 'upload/'; // 设置附件上传（子）目录
    	// 上传文件
    	$info = $upload->uploadOne($_FILES['texture']);
    	if(!$info) {// 上传错误提示错误信息
    		$this->error($upload->getError());
    	}else{
    		$savePath=__ROOT__."Public/upload/";
    		$movePath=$savePath.$md5."/texture/";
    		if(!strstr($texture_exist,$info['name'])){
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error('上传文件名与选择的不一致！','texture?t='.$md5 ,'5');
    		}
    		$texture_exist_list=scandir(__ROOT__.'Public/upload/'.$md5.'/texture/');
    		$texture_exist_num=count($texture_exist_list)-2;
    		if($texture_exist_num>=$texture_num||$texture_exist_num>=20){
    			if(!in_array($info['name'], $texture_exist_list)){
    				$result_delete = @unlink ($savePath.$info['savename']);
    				$this->error('上传的音频文件数量过多！','texture?t='.$md5 ,'5');
    			}
    		}
    		if(!file_exists($movePath)){
    			mkdir($movePath) or exit('创建路径失败，请重试！');
    		}
    		$rename_model_ok=rename($savePath.$info['savename'], $movePath.$info['name']);
    		if(!$rename_model_ok){
    			$result_delete = @unlink ($savePath.$info['savename']);
    			$this->error('上传的音频文件失败，请重试！','texture?t='.$md5 ,'5');
    		}
    	}
    	$this->success('上传音频文件成功','texture?t='.$md5);
    }
    public function change(){
    	$inputs=I('post.');
    	$upload = new \Think\Upload();// 实例化上传类
    	$upload->maxSize = 1048576 ;// 设置附件上传大小
    	$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    	$upload->rootPath = __ROOT__.'Public/'; // 设置附件上传根目录
    	$upload->autoSub  = false;
    	$upload->savePath = 'upload/'.$inputs['folder'].'/'; // 设置附件上传（子）目录
    	// 上传文件
    	$info = $upload->upload();
    	if(!$info) {// 上传错误提示错误信息
    		$this->error($upload->getError());
    	}else{// 上传成功 获取上传文件信息
    		echo '模型缩略图上传成功！<br/>';
    		$preview_path=__ROOT__.'Public/upload/'.$inputs['folder'];
    		foreach ($info as $file){
    		if ($file['key']!='preview')exit('非法输入文件！');
    		$old_preview=$preview_path.'/preview.'.$inputs['preview_ext'];
    		$new_preview_1=$preview_path.'/'.$file['savename'];
    		$new_preview_2=$preview_path.'/preview.'.$file['ext'];		
    		if(file_exists($old_preview)){
    			$result = @unlink ($old_preview);
    			if(!$result)exit("无法删除原模型缩略图，请重试。");
    		}  		
    		echo '原模型缩略图删除成功！<br/>';
    		$rename_ok=rename($new_preview_1, $new_preview_2);
    		if(!$rename_ok)exit("重命名失败。");
    		$Moxing=D("Moxing");
    		$data['preview_ext']=$file['ext'];
    		if (!$Moxing->create($data,2)){ // 创建数据对象
    			// 如果创建失败 表示验证没有通过 输出错误提示信息
    			exit($Moxing->getError());
    		}else{
    			$where="folder='".$inputs['folder']."'";
    			$Moxing->where($where)->save();
    			echo '数据更新成功！<br/>';
    		}
    		}
    	}
    	echo '3秒后跳转至模型信息修改界面...<br/>';
    	header("refresh:3;url=modify?f=".$inputs['folder']);
    }
    public function deleteModel(){
    	$this->title="删除模型";
    	$data=I('post.');
    	if(!$data){
    		$this->display();
    		exit();
    	}
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $data['md5']))
    	{
    		$this->error("模型代码错误！");
    	}
    	if ($data['md5']!==$data['password'])
    	{
    		$this->error("删除密码错误！");
    	}
    	$Moxing=M('Moxing');
    	$result=$Moxing->where("folder='".$data['md5']."'")->delete();
    	if(!$result){
    		$this->error('无此模型！');    	
    	}
    	$result_delete = deldir(__ROOT__."Public/upload/".$data['md5']);
    	$this->success("删除模型成功！");
    }
}