<?php
namespace Home\Controller;
use Think\Controller;
class DnsController extends HomeController {
	public function index(){
		$id=I('get.id/d');
		$Dns=M('Dns');
		$list=$Dns->where('id='.$id.' AND status=1')->find();
		if(!$list)$this->error("DNS不存在或未开启！","__ROOT__");
		$ip=long2ip($list['ip']);			
		redirect('http://'.$ip , 2 , '3D蚂蚁正在寻找您的机器人>>>>');
	}
	public function addDns(){
		$data=I('get.');
		if(!$data)$this->error("无参数！","__ROOT__");
		if(!$data['id'])$this->error("参数错误！","__ROOT__");
		if(!$data['ip'])$this->error("参数错误！","__ROOT__");
		$data['ip']=ip2long($data['ip']);
		//if(!$data['pt'])$this->error("参数错误！");
		if(!$data['pw'])$this->error("参数错误！","__ROOT__");
		$Dns=D('Dns');
		$list=$Dns->where("id=".$data['id']." AND status=1")->find();
		if(!$list)$this->error("DNS记录不存在！");
		if($list['pw']!=$data['pw'])$this->error("密码错误！");
		if(!$Dns->create($data,2)){
			exit($Dns->getError());
		}else {
			$Dns->where('id='.$data['id'])->save();
			$this->success('更新成功！');
		}
	}
}