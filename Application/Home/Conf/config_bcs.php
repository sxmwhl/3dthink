<?php
return array (
		
		// '配置项'=>'配置值'
		// 数据库配置信息
		'DB_TYPE'   => 'mysql', // 数据库类型
		'DB_HOST'   => '127.0.0.1', // 服务器地址
		'DB_NAME'   => 'zhongeco_3dshare', // 数据库名
		'DB_USER'   => 'zhongeco_sxm', // 用户名
		'DB_PWD'    => 'sxm206350482', // 密码
		'DB_PORT'   => 3306, // 端口
		'DB_PREFIX' => 'think_', // 数据库表前缀
		'DB_CHARSET'=> 'utf8', // 字符集
		'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
		/**********本地、远程文件切换*********/
		'FILE_UPLOAD_TYPE' => 'Bcs',
		'UPLOAD_TYPE_CONFIG' => array (
				'AccessKey' => 'v4T5UD1UKe1rCjZpiTKESXes',
				'SecretKey' => 'oz2sA2kd2CcMoHAODiGVq6EwZLiom7iy', // 百度云服务器
				'bucket' => 'x3dfile', // 空间名称
				'rename' => false,
				'timeout' => 3600 
		),
		'TMPL_PARSE_STRING'  =>array(
				'__BCS_X3DFILE__' => 'http://bcs.duapp.com/x3dfile',				
				'__BCS_UPLOAD__' => 'http://bcs.duapp.com/x3dfile/upload',
				//'__BCS_UPLOAD__' => __ROOT__.'/Public/upload',
				'__BCS_PUBLIC__' => 'http://bcs.duapp.com/x3dfile/public',
				//'__BCS_PUBLIC__' => __ROOT__.'/Public',
		/*********本地、远程切换完毕**************/
				'__SITENAME__' => 'web3d模型分享',
		),		
);