<?php
namespace Home\Model;
use Think\Model;
use User\Api\UserApi;
class DiyModel extends Model {
	protected $tablePrefix = 'think_';
	protected $_validate    =   array(
			array('title','require','请为模型命名！'),
			array('title','0,50','模型名称需要精简一些！',0,'length'),
	);
	
}