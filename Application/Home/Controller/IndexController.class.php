<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeController {
    public function index(){
    	$Moxing=M('Moxing');
    	$list1 = $Moxing->where('sign=0')->order('views desc')->limit(8)->select();
    	$list2 = $Moxing->where('sign=0')->order('time_update desc')->limit(8)->select();
    	$this->models1=$list1;
    	$this->models2=$list2;
    	//echo $Moxing->getLastSql();
    	$Category=D('Category');
    	$list3=$Category->get_child_categories(0);
    	$this->categories=$list3;
    	$Diy=M('Diy');
    	$list4=$Diy->where('status=1')->order('views desc')->limit(8)->select();
    	for ($i= 0;$i< count($list4); $i++){
    		$list4[$i]['user']=get_username($list4[$i]['uid']);
    	}
    	$this->diys=$list4;
    	$this->title='首页';
    	$this->keywords='模型分享,在线3d交互,虚拟现实web3d,x3d模型,三维网站,三维模型';
    	$this->description='3D蚂蚁是一个全新的网络虚拟现实平台，可实现网络3D(web3d)模型的在线分享、编辑组建、交互，提供专业的在线虚拟现实、产品3D交互展示服务。';
    	
    	//$this->is_mobile=is_mobile()?1:0;
    	
    	$this->display();
    }
    public function model(){
    	session_start();	  	
    	$md5=I('f');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->error("无此模型！");   
    	}
    	$where="folder='".$md5."'";
    	$Moxing=M('Moxing');
    	$data=$Moxing->where($where)->find();
    	if(!$data)$this->error("无此模型！");
    	$allow_sep='180';
    	if(!isset($_SESSION['post_sep']))$_SESSION['post_sep']=time();
    	if(time() - $_SESSION['post_sep'] > $allow_sep)$_SESSION['post_sep']=time();
    	if($_SESSION['post_sep']==time()){
    		$Moxing->views=$data['views']+1;
    		$Moxing->where('id='.$data['id'])->save();    	
    	}
    	$this->assign('model',$data);
    	$Category=D('Category');
    	$category_path=$Category->get_category_path($data['category']);
    	$this->category_path_i=$category_path;
    	$root_category=$Category->get_child_categories(0);
    	$this->categories=$root_category;
    	$this->title=$data['title'].'模型';
    	//echo $Moxing->getLastSql();
    	$this->keywords='在线3d交互,虚拟现实,x3d模型,'.$data['title'];
    	$this->description=$data['description'];
    	
    	$url='http://www.3dant.cn/index.php/Home/Index/model?f='.$data['folder'];
    	$file=__ROOT__.'Public/upload/'.$data['folder'].'/erweima.png';
    	think_phpqrcode($url,$file); 
    	
    	//$this->is_mobile=is_mobile()?1:0;
    	   	
    	$this->display('model');
    }
    public function modelIn(){
    	$md5=I('f');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->error("无此模型！");    		
    	}
    	$Moxing=M('Moxing');
    	$where="folder='".$md5."'";
    	$data=$Moxing->where($where)->find();
    	if(!$data)$this->error("无此模型！");
    	$data['hl_on']=$data['hl_on']==0?"false":"true";
    	$data['dl_on']=$data['dl_on']==0?"false":"true";
    	$this->assign('model',$data);
    	$this->title=$data['title'];
    	//echo $Moxing->getLastSql();
    	$this->keywords='在线3d交互,虚拟现实,x3d模型,'.$data['title'];
    	$this->description=$data['description'];
    	$this->display('modelIn');
    }
    public function search(){
    	$Category=D('Category');
    	$list3=$Category->get_child_categories(0);
    	$this->categories=$list3;
    	$Moxing=M('Moxing');
    	$keywords=I('keywords');
    	$map['_string'] = "concat (title,description) like '%".$keywords."%'";
    	$list = $Moxing->where($map)->select();
    	$this->assign('model',$list);
    	$this->assign('keywords',$keywords);
    	$this->title=$keywords.' 搜索结果';
    	//echo $Moxing->getLastSql();
    	$this->display('search');
    }
    public function diy(){
    	session_start();
    	$allow_sep='180';
    	$id=I('id',0,'int');
    	$Diy=M('Diy');
    	$data=$Diy->where("id=".$id)->find();
    	if(!$data)$this->error("无此DIY！");
    	$Member=M('Member');
    	$data2=$Member->where('uid='.$data['uid'])->find();    	
    	if(!isset($_SESSION['post_sep']))$_SESSION['post_sep']=time();
    	if(time() - $_SESSION['post_sep'] > $allow_sep)$_SESSION['post_sep']=time();
    	if($_SESSION['post_sep']==time()){
    		$Diy->views=$data['views']+1;
    		$Diy->where('id='.$id)->save();
    	} 
    	$Category=D('Category');
    	$list3=$Category->get_child_categories(0);
    	$this->categories=$list3;
    	$this->user=$data2;   	
    	$this->diy=$data;
    	$this->title="DIY模型《".$data['title']."》";  
    	$this->keywords=$data['title']."web3d模型,在线3d交互,虚拟现实";
    	$this->description=$data['description'];
    	
    	$url='http://www.3dant.cn/index.php/Home/Index/diy?id='.$data['id'];
    	$file=__ROOT__.'Public/diy/'.$data['uid'].'/'.$data['id'].'/erweima.png';
    	think_phpqrcode($url,$file);
    	
    	//$this->is_mobile=is_mobile()?1:0;
    	
    	$this->display();  	
    }
    public function diyIn(){
    	$id=I('id',0,'int');
    	$Diy=M('Diy');
    	$list=$Diy->where("id=".$id)->find();
    	if(!$list)$this->error("无此DIY！");
    	$this->diy=$list;
    	$this->title=$list['title'];
    	$this->display();
    }
    public function user(){
    	$uid=I('id',0,'int');    	
    	//if($uid===0)$this->error('用户不存在');
    	$Member=M("Member");
    	$result=$Member->where('uid='.$uid)->find();
    	if(!$result)$this->error('用户不存在');
    	$this->user=$result;
    	$Diy=M('Diy');
    	$list=$Diy->where('uid='.$uid)->select();
    	$this->diys=$list;
    	$Moxing=M('Moxing');
    	$list2=$Moxing->where('uid='.$uid)->select();
    	$this->moxings=$list2;
    	$Category=D('Category');
    	$list3=$Category->get_child_categories(0);
    	$this->categories=$list3;
    	$this->title=$result['nickname']."的web3d模型";    	
    	$this->description=$result['nickname']."分享web3d模型和在线编辑的虚拟世界。";
    	$this->display();
    }
    public function tryEdit(){
    	$this->title="web3d模型在线编辑体验页面";
    	$this->keywords="在线3d交互,虚拟现实,x3d模型,在线编辑3d模型";
    	$this->description="在线编辑3d模型体验页面，可插入基本模型，已分享模型，在线编辑模型颜色、尺寸、位置。";
    	$this->display();
    }
}