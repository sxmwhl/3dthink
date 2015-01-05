<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	$Moxing=M('Moxing','think_');
    	$list1 = $Moxing->where('sign=0')->order('time_update')->limit(6)->select();
    	$list2 = $Moxing->where('sign=0')->order('views')->limit(6)->select();
    	$this->models1=$list1;
    	$this->models2=$list2;
    	//echo $Moxing->getLastSql();
    	$Category=D('Category');
    	$list3=$Category->get_child_categories(0);
    	$this->categories=$list3;
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
    	$Moxing=M('Moxing','think_');
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
    	//echo $Moxing->getLastSql();
    	$this->display('model');
    }
    public function modelIn(){
    	$Moxing=M('Moxing','think_');
    	$md5=I('f');
    	if (!preg_match("/^([a-fA-F0-9]{32})$/", $md5))
    	{
    		$this->display("Public:404");
    		exit();
    	}
    	$where="folder='".$md5."'";
    	$data=$Moxing->where($where)->find();
    	$data['hl_on']=$data['hl_on']==0?"false":"true";
    	$data['dl_on']=$data['dl_on']==0?"false":"true";
    	$this->assign('model',$data);
    	//echo $Moxing->getLastSql();
    	$this->display('modelIn');
    }
    public function search(){
    	$Category=D('Category');
    	$list3=$Category->get_child_categories(0);
    	$this->categories=$list3;
    	$Moxing=M('Moxing','think_');
    	$keywords=I('keywords');
    	$map['_string'] = "concat (title,description) like '%".$keywords."%'";
    	$list = $Moxing->where($map)->select();
    	$this->assign('model',$list);
    	$this->assign('keywords',$keywords);
    	//echo $Moxing->getLastSql();
    	$this->display('search');
    }
}