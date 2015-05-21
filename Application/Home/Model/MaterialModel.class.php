<?php
namespace Home\Model;
use Think\Model;
class MaterialModel extends Model {
	protected $tablePrefix = 'think_';
	public $Material;
	 /* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '标题不能为空'),
    	array('description', 'require', '摘要不能为空'),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('uid', 'session', self::MODEL_INSERT, 'function', 'user_auth.uid'),
        array('title', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
        array('description', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
        array('views', 0, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
    );
	public function  __construct(){
		parent::__construct();
		$this->Material=M('Material','think_');
	}
	function get_category_Materials($cate_id=0){
		$Materials=$this->Material->where('category_id='.$cate_id)->field('*')->order('views')->select();
		return $Materials;
	}
}