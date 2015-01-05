<?php
namespace Home\Model;
use Think\Model;
class CategoryModel extends Model {
	protected $tablePrefix = 'think_';
	public $Category;
	//自动验证
	protected $_validate = array(
			array('cate_name','require','请为分类命名！'),
			array('cate_dir','require','请为分配分类目录'),
	);
	/*定义自动完成
	protected $_auto    =   array(
			array('cate_arrparentid','',1),
			array('cate_arrchildid','',1),
			array('cate_childcount','0',1),

	);*/
	public function  __construct(){
		parent::__construct();
		$this->Category=M('Category','think_');
	}
	function get_one_category($cate_id = 0) {
		$row=$this->Category->where("cate_id=".$cate_id)->find();	
		//$row = load_cache('category_'.$cate_id) ? load_cache('category_'.$cate_id) : $DB->fetch_one("SELECT cate_id, root_id, cate_name, cate_dir, cate_arrparentid, cate_arrchildid, cate_childcount, cate_postcount FROM ".$DB->table('categories')." WHERE cate_id=$cate_id LIMIT 1");
		$get_one_category=$row;
		$row=null;
		return $get_one_category;
	}
	function get_category_path($cate_id = 0) {
		$cate = $this->get_one_category($cate_id);
		if (!isset($cate)) return '';
		$category_path=$this->Category->where("cate_id IN (".$cate_id.",".$cate['cate_arrparentid'].")")->field('cate_id,cate_name')->select();
		//$sql = "SELECT cate_id, cate_name FROM ".$DB->table('categories')." WHERE cate_id IN (".$cate_id.','.$cate['cate_arrparentid'].")";
		//$categories = $DB->fetch_all($sql);
		return $category_path;
	}
	function get_category_option($root_id = 0, $cate_id = 0, $level_id = 0) {		
		$categories = $this->Category->where('root_id='.$root_id)->field('cate_id,cate_name')->order('cate_order ASC,cate_id ASC')->select();
		//$sql = "SELECT cate_id, cate_name FROM ".$DB->table('categories')." WHERE root_id=$root_id ORDER BY cate_order ASC, cate_id ASC";
		//$categories = $DB->fetch_all($sql);
		$optstr = '';
		foreach ($categories as $cate) {
			$optstr .= '<option value="'.$cate['cate_id'].'"';
			if ($cate_id > 0 && $cate_id == $cate['cate_id']) $optstr .= ' selected';	
			if ($level_id == 0) {
				$optstr .= ' style="background: #EEF3F7;">';
				$optstr .= '├'.$cate['cate_name'];
			} else {
				$optstr .= '>';
				for ($i = 2; $i <= $level_id; $i++) {
					$optstr .= '│&nbsp;&nbsp;';
				}
				$optstr .= '│&nbsp;&nbsp;├'.$cate['cate_name'];
			}
			$optstr .= '</option>';
			$optstr .= $this->get_category_option($cate['cate_id'], $cate_id, $level_id + 1);
		}
		unset($categories);
		return $optstr;
	}
	function  get_root_id($cate_id){
		$get_root_id=$this->Category->where('cate_id='.$cate_id)->field('root_id')->find();
		return $get_root_id['root_id'];
	}
	function  get_cate_name($cate_id){
		$get_cate_name=$this->Category->where('cate_id='.$cate_id)->field('cate_name')->find();
		return $get_cate_name['cate_name'];
	}
	function get_root_ids($cate_id) {
		$str="";
		$i=0;
		while ($cate_id>0){
			$cate_id=$this->get_root_id($cate_id);
			$str=$cate_id.','.$str;
			$i++;
		}
		$get_root_ids=rtrim($str, ",") ;
		$cate_id=null;
		$i=null;
		$str=null;
		return $get_root_ids;
	}
	function get_root_string($cate_id) {
		$root_name='';
		$cate=$this->get_one_category($cate_id);
		$cate_root_arr=explode(',',$cate['cate_arrparentid']);
		foreach ($cate_root_arr as $root_id){			
			$str=$this->get_cate_name($root_id).'/';	
			$root_name=$root_name.$str;			
		}		
		$get_root_string=rtrim($root_name, "/") ;
		$cate=null;
		$cate_root_arr=null;
		$str=null;
		$root_name=null;
		return $get_root_string;
	}
	function get_child_categories($cate_id=0){
		$categories = $this->Category->where('root_id='.$cate_id)->order('cate_order ASC,cate_id ASC')->select();
		return $categories;
	}	 
	function get_moxing_count($cate_id = 0) {
		$Moxing=M('Moxing','think_');			
		$moxings_id = $Moxing->where('category='.$cate_id)->field('id')->select();
		$get_moxing_count = count($moxings_id);
		return $get_moxing_count;
	}
}