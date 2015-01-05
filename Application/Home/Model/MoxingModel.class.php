<?php
namespace Home\Model;
use Think\Model;
class MoxingModel extends Model {
	protected $tablePrefix = 'think_';
	public $Moxing;
	//自动验证
	protected $_validate    =   array(
			array('title','require','请为模型命名！'),
			array('title','0,50','模型名称需要精简一些！',0,'length'),
			array('description','require','请对模型进行描述！'),
			array('description','0,300','模型描述过长！',0,'length'),
			array('category','require','请为模型选择分类！'),
			array('category','number','分类数据类型必须为数字！'),
			array('email','email','邮箱格式不正确！',2),
			array('email','0,30','邮箱名称过长！',2,'length'),	
	);
	// 定义自动完成
	protected $_auto    =   array(
			array('title','未命名',1),
			array('description','尚无描述',1),
			array('category','0',1),
			array('creator','佚名',1),
			array('sign','0',1),
			array('views','1',1),			
			array('hl_on','1',1),
			array('dl_on','0',1),			
			array('vp_position','0,0,10',1),
			array('vp_orientation','0,0,0,1',1),

	);
	public function  __construct(){
		parent::__construct();
		$this->Moxing=M('Moxing','think_');
	}
	function get_category_moxings($cate_id=0){
		$moxings=$this->Moxing->where('category='.$cate_id)->field('*')->order('views')->select();
		return $moxings;
	}
}