<?php
namespace Home\Model;
use Think\Model;
class DnsModel extends Model {
	protected $tablePrefix = 'think_';
	public $Dns;
	 /* 自动验证规则 */
    protected $_validate = array(
        array('ip', 'require', 'ip不能为空'),
    	array('port', 'require', '端口不能为空'),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        //array('uid', 'session', self::MODEL_INSERT, 'function', 'user_auth.uid'),
        array('title', 'htmlspecialchars', self::MODEL_BOTH, 'function'),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
    );
	public function  __construct(){
		parent::__construct();
		$this->Dns=M('Dns','think_');
	}
}