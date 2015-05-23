<?php
namespace Home\Controller;
use Think\Controller;
class HelpController extends HomeController {
	public function index(){
		$Category=D('Category');
		$list3=$Category->get_child_categories(0);
		$this->categories=$list3;
		$Article=M('Article');
		$list=$Article->where('display=1')->order('views')->select();
		$this->articles=$list;
		$this->title="帮助文档";
		$this->keywords='3d蚂蚁网使用帮助文档,在线3d交互帮助,虚拟现实web3d帮助,x3d模型,三维网站,三维模型';
		$this->description='3D蚂蚁网网络虚拟现实平台使用帮助，如何分享模型，在线编辑修改模型，模型浏览交互模式帮助。';
		$this->display();
	}
	public function navigation(){
		$Category=D('Category');
		$list3=$Category->get_child_categories(0);
		$this->categories=$list3;
		$this->title="模型交互模式说明";
		$this->display();
	}
	public function browserSupport(){
		$Category=D('Category');
		$list3=$Category->get_child_categories(0);
		$this->categories=$list3;
		$this->title="浏览器支持";
		$this->description="对WebGL支持的浏览器的汇总。";
		$this->display();
	}
	public function about(){
		$Category=D('Category');
		$list3=$Category->get_child_categories(0);
		$this->categories=$list3;
		$this->title="关于3D蚂蚁";
		$this->display();
	}
	public function article(){
		$id=I('get.id/d',0);
		$Article=M('Article');
		$list=$Article->where('id='.$id.' AND display=1')->find();
		if(!$list)$this->error("文章不存在！","index");
		$this->article=$list;
		$this->title=$list['title'];
		$this->description=$list['description'];
		$this->display();
	}
	public function material(){
		$this->title="web3d在线材质编辑器";
		$this->display();
	}
	public function music(){
		$this->title="音乐播放器";
		$this->display();
	}
	public function music2(){
		$this->title="音乐播放器";
		$this->display();
	}
}