<?php
namespace Home\Model;
use Think\Model;
use User\Api\UserApi;
class DiyModel extends Model {
	protected $tablePrefix = 'think_';
	protected $_validate    =   array(
			array('title','require','请为模型命名！'),
			array('title','0,50','模型名称需要精简一些！',0,'length'),
			array('uid','number','用户id格式错误！'),
			array('description','require','请对模型进行描述!'),
			array('description','0,300','描述内容过多!',0,'length'),
			array('views','number','浏览次数格式错误！',2),
			array('status',array(0,1,2),'状态数值错误！',2,'in'),
	);	
}