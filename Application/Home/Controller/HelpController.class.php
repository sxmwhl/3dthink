<?php
namespace Home\Controller;
use Think\Controller;
class HelpController extends Controller {
	public function index(){
		$Category=D('Category');
		$list3=$Category->get_child_categories(0);
		$this->categories=$list3;
		$this->title="帮助文档";
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
}