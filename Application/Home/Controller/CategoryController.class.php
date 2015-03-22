<?php
namespace Home\Controller;
use Think\Controller;
class CategoryController extends Controller {	
    public function index(){
    	$cate_id=I('cate');
    	if(empty($cate_id))$cate_id=0;
    	$Category=D('Category');
    	$cate_name=$Category->get_one_category($cate_id);
    	$category_path=$Category->get_category_path($cate_id);
    	$this->category_path=$category_path;
    	//echo $Category->getLastSql();
    	$categories = $Category->get_child_categories($cate_id);
    	$this->categories=$categories;
    	$Moxing=D('Moxing');
    	$moxings = $Moxing->get_category_moxings($cate_id);
    	$this->moxings=$moxings;
    	$title=empty($cate_name['cate_name'])?'未':$cate_name['cate_name'];
    	$this->title=$title.'分类模型及子分类';
    	$this->display();
    }
    public function add(){
    	$cate_id=I('cate');
    	$Category=D('Category');
    	if(isset($cate_id))$this->cate_id=$cate_id;
    	$category_option = $Category->get_category_option(0, $cate_id, 0);
    	$this->category_option=$category_option;    	
    	//echo $Moxing->getLastSql();
    	$this->title='添加分类';
    	$this->display('add');
    }
    public function del(){
    	$cate_id=I('cate');
    	$Category=D('Category');
    	$row=$Category->get_one_category($cate_id);
    	if ($row['cate_postcount']!=0)$this->error('此分类下含有模型，无法删除！');
    	if ($row['cate_childcount']!=0)$this->error('此分类下含有子类，无法删除！');
    	if($Category->delete($cate_id)!=1)$this->error('删除分类出差，请重试！');
    	$row2=$Category->get_one_category($row['root_id']);
    	if ($row2['cate_childcount']>0){
    		$cate_childcount=$row2['cate_childcount']-1;
    		$Category-> where('cate_id='.$row2['cate_id'])->setField('cate_childcount',$cate_childcount);
    	}
    	$this->category_option=$category_option;
    	$row=null;
    	$row2=null;
    	$category_option=null;
    	//echo $Category->getLastSql();
    	header("Location:".__MODULE__."/category/lists");
    	//$this->display('add');
    }
    public function edit(){
    	$cate_id=I('cate');
    	$Category=D('Category');
    	$root_id=$Category->where('cate_id='.$cate_id)->field('root_id')->find();    	
    	$this->category_option=$Category->get_category_option(0, $root_id['root_id'], 0);
    	$onecategory=$Category->get_one_category($cate_id);
    	$this->onecategory=$onecategory;    	
    	//echo $Category->getLastSql();
    	$this->title=$onecategory['cate_name'].'分类编辑';
    	$this->display('edit');
    }
    public function lists(){
    	$cate_id=I('cate');
    	if(empty($cate_id))$cate_id=0;
    	$Category=D('Category');
    	$categories = $Category->get_child_categories($cate_id);
    	$this->cate_id=$cate_id;
    	$this->categories=$categories;
    	$onecategory=$Category->get_one_category($cate_id);
    	$onecategory['cate_name']=empty($onecategory['cate_name'])?'根':$onecategory['cate_name'];
    	$this->title=$onecategory['cate_name'].'分类列表';
    	$this->root_id=$onecategory['root_id'];
    	//echo $Moxing->getLastSql();
    	$this->display('lists');
    }
    public function save(){
    	$inputs=I('post.');
    	$Category=D('Category');
    	//添加父类字符串
    	if($inputs['root_id']==0){
    		$inputs['cate_arrparentid']='0';
    	}else{
    		$inputs['cate_arrparentid']=$Category->get_root_ids($inputs['root_id']).','.$inputs['root_id'];
    	}
    	//计算分类等级    	
    	$inputs['cate_level']= substr_count($inputs['cate_arrparentid'],',')+1 ;
    	//判断同级分类是否重名
    	$where="cate_name='".$inputs['cate_name']."' and cate_level=".$inputs['cate_level'];
    	$same_name=$Category->where($where)->field('cate_id')->find();
    	//echo $Category->getLastSql();
    	if(!empty($same_name))exit('相同等级分类重名！');
    	if (!$Category->create($inputs,1)){ // 创建数据对象
    		// 如果创建失败 表示验证没有通过 输出错误提示信息
    		exit($Category->getError());
    	}else{
    		// 验证通过 写入新增数据
    		$cate_id=$Category->add();
    		if ($cate_id==false)exit('添加分类出错！');
    	} 
    	//给父分类添加子分类数 
    	if($inputs['root_id']>0){
    		$row=$Category->get_one_category($inputs['root_id']);
    		$cate_childcount=$row['cate_childcount']+1;
    		$Category-> where('cate_id='.$inputs['root_id'])->setField('cate_childcount',$cate_childcount);
    	}    	
    	$row=null;
    	//$Category->add_category_update($cate_id);
    	echo '添加路径成功：'.$Category->get_root_string($cate_id)."/".$inputs['cate_name'];
    	header("Location:".__MODULE__."/category/lists?cate=".$inputs['root_id']);
    	//$this->display('modelIn');
    }
    public function update(){
    	$inputs=I('post.');
    	$Category=D('Category');
    	$row=$Category->get_one_category($inputs['cate_id']);
    	if($inputs['root_id']!=$row['root_id'])exit('请不要更改分类父目录！');
    	if (!$Category->create($inputs,2)){ // 创建数据对象
    		// 如果创建失败 表示验证没有通过 输出错误提示信息
    		exit($Category->getError());
    	}else{
    		// 验证通过 写入新增数据
    		//echo $Moxing->title;
    		$Category->where('cate_id='.$inputs['cate_id'])->save();
    	}
    	//$this->display('modelIn');
    	header("Location:".__MODULE__."/category/lists?cate=".$inputs['root_id']);
    }
}