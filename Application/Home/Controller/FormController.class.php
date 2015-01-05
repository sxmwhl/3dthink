<?php
namespace Home\Controller;
use Think\Controller;
class FormController extends Controller {
    public function index(){
      $this->display();
    }
    public function upload(){
    	$upload = new \Think\Upload();// 实例化上传类
    	$upload->maxSize = 3145728 ;// 设置附件上传大小
    	$upload->exts = array('jpg', 'gif', 'png', 'jpeg','x3d');// 设置附件上传类型    	
    	$upload->rootPath = __ROOT__.'Public/'; // 设置附件上传根目录
    	$upload->autoSub  = false;
    	$upload->savePath = 'upload/'; // 设置附件上传（子）目录
    	// 上传文件
    	$info = $upload->upload();
    	if(!$info) {// 上传错误提示错误信息
    		$this->error($upload->getError());
    	}else{// 上传成功 获取上传文件信息
    		foreach($info as $file){    
    			if ($file['key']=='model'){
    				//是否为x3d格式
    				if($file['ext']!="x3d"&&$file['ext']!="X3D")exit(模型格式不正确！);
    				//>>>添加判断MD5是否重复 超链接需修改##############################
    				$Moxing_check=M('Moxing','think_');
    				$where_md5="folder='".$file['md5']."'";
    				$result_check=$Moxing_check->where($where_md5)->find();
   				if(!empty($result_check)){
    					exit("该模型已被上传过！<a target='_blank' href='".__MODULE__."/index/model?f=".$file['md5']."'>点击查看</a>");
    				}
    				$model_savename_empty=$file['savename'];
    				$model_md5_empty=$file['md5'];  				
    			}
    			if ($file['key']=='preview'){
    				$preview_savename_empty=$file['savename'];
    				$preview_ext_empty=$file['ext'];
    			}	
    			if ($file['key']=='texture'){
    				$texture_name_empty=$file['name'];
    				$texture_savename_empty=$file['savename'];    				
    			}		
    		//	$string=implode(',',$file);
    		//	echo $string;
    		//	echo $file['key'].'';
    		}
    		if(empty($model_savename_empty))exit("分享模型失败，请确认模型文件是否正确！");    		
    		if(empty($preview_savename_empty))exit("分享模型失败，请确认缩略图文件是否正确！");
    		$uploadPath=__ROOT__.'Public/upload/';
    		$movePath=$uploadPath.$model_md5_empty.'/';
    		if(!file_exists($movePath)){
    		mkdir($movePath) or exit('创建路径失败，请重试！');
    		}
    		$rename_model_ok=rename($uploadPath.$model_savename_empty, $movePath.'model.x3d');
    		$rename_preview_ok=rename($uploadPath.$preview_savename_empty, $movePath.'preview.'.$preview_ext_empty);
    		if(!empty($texture_name_empty)){
    			if(!file_exists($movePath.'texture/')){
    				mkdir($movePath.'texture/') or exit('创建路径失败，请重试！');
    			}
    			$rename_texture_ok=rename($uploadPath.$texture_savename_empty, $movePath.'texture/'.$texture_name_empty);
    			if(!$rename_texture_ok)exit("系统出错，请重试！1");
    		}
    		//echo $rename_model_ok;
    		if(!$rename_model_ok)exit("系统出错，请重试！2");
    		if(!$rename_preview_ok)exit("系统出错，请重试！3");
    		$Moxing=D('Moxing');
    		$inputs['folder']=$model_md5_empty;
    		$inputs['preview_ext']=$preview_ext_empty;
    		$inputs['time_update']=date('Y-m-d H:i:s',time());
    		$inputs['ip_upload']=get_client_ip();
    		if (!$Moxing->create($inputs)){ // 创建数据对象
    			// 如果创建失败 表示验证没有通过 输出错误提示信息
    			exit($Moxing->getError());
    			 
    		}else{
    			// 验证通过 写入新增数据
    			$Moxing->add();    			 
    		}
    		header("refresh:3;url=modify?f=".$model_md5_empty);
    		echo '模型上传成功，请稍等...<br>三秒后自动跳转至预览页面，请对模型信息进行完善...';
    		//#################上传完毕#######################    		
    	}	
    }
    public function modify(){   
    	$md5=I('f');
    	$cate=I('cate');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$Category=D('Category');
    	$category_option = $Category->get_category_option(0,$cate,0);
    	$this->category_option=$category_option;
    	$Moxing=M('Moxing','think_');
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
    	$this->model=$model_show;
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
    	}
    	//echo $Moxing->getLastSql(); //最后运行sql语句
    	header("Location:".__MODULE__."/index/model?f=".$inputs['folder']);
    	//$this->display('public:jump');
    }
}