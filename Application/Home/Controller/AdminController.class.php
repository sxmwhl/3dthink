<?php
namespace Home\Controller;
use User\Api\UserApi;
use Think\Controller;
class AdminController extends Controller {
	protected function _initialize(){
		if ( !is_administrator() ) {
			$this->error( '你不是管理员！',U('User/login') );
		}
	}
    public function index(){  
    	$Article=M('Article');
    	$list=$Article->where('display=1')->order('views')->select();
    	$this->articles=$list;
    	$this->title='管理平台';
    	$this->display();      
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
    public function addArticle(){
    	//$this->info=$_SESSION["user_auth"];
    	//$this->info=session('user_auth');
    	//$this->info2=session('user_auth_sign');
    	//$this->info2=$_SESSION["user_auth"]['uid'];
    	$inputs=I('post.');
    	if(!$inputs){
    		$this->title="添加文档";
    		$this->display();
    		exit();
    	}
    	$Article=D('Article');
    	if(!$Article->create()){
    		$this->error('创建数据失败！');
    	}
    	$result = $Article->add(); // 写入数据到数据库
    	if(!$result){
    		$this->error('添加数据失败！');
    	}
    	// 如果主键是自动增长型 成功后返回值就是最新插入的值
    	$insertId = $result;
    	$this->success('添加成功！','index');
    }
    public function deleteArticle(){
    	$id=I('get.id/d');
    	if(!$id){
    		$this->error('参数错误！','index');
    	}
    	$Article=M('Article');
    	$res=$Article->where('id='.$id)->delete();
    	if(!$res)$this->error('删除失败！','index');
    	$this->success('删除成功！' ,'index');
    }
    public function editArticle(){
    	$id=I('get.id/d',0);
    	if($id==0)$this->error('参数错误！','index');
    	$data=I('post.');
    	if(!$data){
    		$Article=M('Article');
    		$list=$Article->where('id='.$id)->find();
    		$this->article=$list;
    		$this->display();
    		exit();
    	}
    	$Article=D('Article');
    	if(!$Article->create()){
    		$this->error('创建数据失败！');
    	}
    	$res=$Article->where('id='.$id)->save();
    	if(!$res)$this->error('更新数据失败！');
    	$this->success('编辑成功！' ,'index');
    }
}