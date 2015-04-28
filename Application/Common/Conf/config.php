<?php
return array(
	//'配置项'=>'配置值'
	//管理员ID
		'URL_CASE_INSENSITIVE' => false,
		'USER_ADMINISTRATOR' => 1,
		//数据库配置信息
		'USER_ALLOW_REGISTER'   => true, // 用户注册
		'WEB_SITE_CLOSE'   => true, // 站点开关
		'DB_TYPE'   => 'mysql', // 数据库类型
		'DB_HOST'   => '127.0.0.1', // 服务器地址
		'DB_NAME'   => '3dshare', // 数据库名
		'DB_USER'   => 'root', // 用户名
		'DB_PWD'    => '', // 密码
		'DB_PORT'   => 3306, // 端口
		'DB_PREFIX' => 'think_', // 数据库表前缀
		'DB_CHARSET'=> 'utf8', // 字符集
		'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
	//'DB_DSN' => 'mysql://root@localhost:3306/3dshare#utf8'
		'TMPL_PARSE_STRING'  =>array(
				'__BCS_X3DFILE__' => 'http://bcs.duapp.com/x3dfile',
		
				//'__BCS_UPLOAD__' => 'http://bcs.duapp.com/x3dfile/upload',
				'__BCS_UPLOAD__' => __ROOT__.'/Public/upload',
				//'__BCS_PUBLIC__' => 'http://bcs.duapp.com/x3dfile/public',
				'__BCS_PUBLIC__' => __ROOT__.'/Public',
				/*********本地、远程切换完毕**************/
				'__SITENAME__' => '全新的网络虚拟现实平台-3D蚂蚁',
		),
		'THINK_EMAIL' => array(		
				'SMTP_HOST'   => 'smtp.3dant.cn', //SMTP服务器		
				'SMTP_PORT'   => '465', //SMTP服务器端口		
				'SMTP_USER'   => 'sxm@3dant.cn', //SMTP服务器用户名		
				'SMTP_PASS'   => 'Lcx206350482', //SMTP服务器密码		
				'FROM_EMAIL'  => 'sxm@3dant.cn', //发件人EMAIL		
				'FROM_NAME'   => '3D蚂蚁工作组', //发件人名称		
				'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）		
				'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）		
		),
);