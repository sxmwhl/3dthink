<?php
namespace Home\Controller;
use Think\Controller;
class CategoryController extends Controller {	
    public function index(){
    	$cate_id=I('cate');
    	if(empty($cate_id))$cate_id=0;
    	$Category=D('Category');
    	$category_path=$Category->get_category_path($cate_id);
    	$this->category_path=$category_path;
    	//echo $Category->getLastSql();
    	$categories = $Category->get_child_categories($cate_id);
    	$this->categories=$categories;
    	$Moxing=D('Moxing');
    	$moxings = $Moxing->get_category_moxings($cate_id);
    	$this->moxings=$moxings;
    	
    	$this->display();
    }
    public function add(){
    	$cate_id=I('cate');
    	$Category=D('Category');
    	$category_option = $Category->get_category_option(0, $cate_id, 0);
    	$this->category_option=$category_option;    	
    	//echo $Moxing->getLastSql();
    	$this->display('add');
    }
    public function del(){
    	$cate_id=I('cate');
    	$Category=D('Category');
    	$row=$Category->get_one_category($cate_id);
    	if ($row['cate_postcount']!=0)exit('此分类下含有模型，无法删除！');
    	if ($row['cate_childcount']!=0)exit('此分类下含有子类，无法删除！');
    	if($Category->delete($cate_id)!=1)exit('删除分类出差，请重试！');
    	$row2=$Category->get_one_category($row['root_id']);
    	if ($row2['cate_childcount']>0){
    		$cate_childcount=$row2['cate_childcount']-1;
    		$Category-> where('cate_id='.$row2['cate_id'])->setField('cate_childcount',$cate_childcount);
    	}
    	$this->category_option=$category_option;
    	$row=null;
    	$row2=null;
    	$category_option=null;
    	echo $Category->getLastSql();
    	//$this->display('add');
    }
    public function edit(){
    	$cate_id=I('cate');
    	$Category=D('Category');
    	$root_id=$Category->where('cate_id='.$cate_id)->field('root_id')->find();    	
    	$this->category_option=$Category->get_category_option(0, $root_id['root_id'], 0);
    	$this->onecategory=$Category->get_one_category($cate_id);    	
    	//echo $Category->getLastSql();
    	$this->display('edit');
    }
    public function lists(){
    	$cate_id=I('cate');
    	if(empty($cate_id))$cate_id=0;
    	$Category=D('Category');
    	$categories = $Category->get_child_categories($cate_id);
    	$this->categories=$categories;
    	//echo $Moxing->getLastSql();
    	$this->display('lists');
    }
    public function save(){
    	$inputs=I('post.');
    	$Category=D('Category');
    	if($inputs['root_id']==0){
    		$inputs['cate_arrparentid']='0';
    	}else{
    		$inputs['cate_arrparentid']=$Category->get_root_ids($inputs['root_id']).','.$inputs['root_id'];
    	}    	
    	if (!$Category->create($inputs,1)){ // 创建数据对象
    		// 如果创建失败 表示验证没有通过 输出错误提示信息
    		exit($Category->getError());
    	}else{
    		// 验证通过 写入新增数据
    		//echo $Moxing->title;
    		$cate_id=$Category->add();
    		if ($cate_id==false)exit('添加分类出错！');
    	}  
    	if($inputs['root_id']>0){
    		$row=$Category->get_one_category($inputs['root_id']);
    		$cate_childcount=$row['cate_childcount']+1;
    		$Category-> where('cate_id='.$inputs['root_id'])->setField('cate_childcount',$cate_childcount);
    	}    	
    	$row=null;
    	//$Category->add_category_update($cate_id);
    	echo '添加路径成功：'.$Category->get_root_string($cate_id)."/".$inputs['cate_name'];
    	//$this->display('modelIn');
    }
    public function update(){
    	$inputs=I('post.');
    	$Category=D('Category');
    	if (!$Category->create($inputs,2)){ // 创建数据对象
    		// 如果创建失败 表示验证没有通过 输出错误提示信息
    		exit($Category->getError());
    	}else{
    		// 验证通过 写入新增数据
    		//echo $Moxing->title;
    		$Category->where('cate_id='.$inputs['cate_id'])->save();
    	}
    	//$this->display('modelIn');
    	header("Location:".__ROOT__."/Home/category/lists?cate=".$inputs['root_id']);
    }
}