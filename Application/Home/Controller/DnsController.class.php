<?php
namespace Home\Controller;
use Think\Controller;
class DnsController extends HomeController {
	public function index(){
		$id=I('get.id/d');
		$Dns=M('Dns');
		$list=$Dns->where('id='.$id.' AND status=1')->find();
		if(!$list)$this->error("DNS不存在或未开启！",U('Index/index'));
		$ip=long2ip($list['ip']);			
		redirect('http://'.$ip , 2 , '3D蚂蚁正在寻找您的机器人>>>>');
	}
	public function updateDns(){
		$data=I('get.');
		if(!$data)exit("无参数");
		if(!$data['id'])exit("参数错误");
		if(!$data['ip'])exit("参数错误");
		$data['ip']=ip2long($data['ip']);
		//if(!$data['pt'])$this->error("参数错误！");
		if(!$data['pw'])exit("参数错误");
		$Dns=D('Dns');
		$list=$Dns->where("id=".$data['id']." AND status=1")->find();
		if(!$list)exit("DNS记录不存在！");
		if($list['pw']!=$data['pw'])exit("密码错误");
		if(!$Dns->create($data,2)){
			exit($Dns->getError());
		}else {
			$Dns->where('id='.$data['id'])->save();
			exit("更新成功");
			//$this->success('更新成功！');
		}
	}
}