<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
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
    	$this->diys=$list4;
    	$this->title='首页';
    	$this->keywords='模型分享,3d模型,web3d,x3d模型,三维网站,三维模型';
    	$this->display();
    }
    public function model(){
    	session_start();    	  	
    	$md5=I('f');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$where="folder='".$md5."'";
    	$Moxing=M('Moxing');
    	$data=$Moxing->where($where)->find();
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
    	$this->category_path=$category_path;
    	$root_category=$Category->get_child_categories(0);
    	$this->categories=$root_category;
    	$this->title=$data['title'].'模型';
    	//echo $Moxing->getLastSql();
    	$this->display('model');
    }
    public function modelIn(){
    	$md5=I('f');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$Moxing=M('Moxing');
    	$where="folder='".$md5."'";
    	$data=$Moxing->where($where)->find();
    	$data['hl_on']=$data['hl_on']==0?"false":"true";
    	$data['dl_on']=$data['dl_on']==0?"false":"true";
    	$this->assign('model',$data);
    	$this->title=$data['title'];
    	//echo $Moxing->getLastSql();
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
    	$this->display();  	
    }
    public function diyIn(){
    	$id=I('id',0,'int');
    	$Diy=M('Diy');
    	$list=$Diy->where("id=".$id)->find();
    	$this->diy=$list;
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
    	$this->title=$result['nickname']."的3d模型";
    	$this->display();
    }
}