<?php
namespace Home\Controller;
use Think\Controller;
class HelpController extends Controller {
	public function index(){
		$this->title="模型浏览模式说明";
		$this->display();
	}
}