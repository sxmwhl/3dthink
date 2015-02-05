<?php
header("Content-Type: text/html;charset=utf-8"); 
$con= new mysqli("localhost","root","");
$con->select_db("3dshare");
$sqls = "
DROP TABLE IF EXISTS `think_moxing`;
CREATE TABLE think_moxing
(
id int unsigned NOT NULL AUTO_INCREMENT, 
PRIMARY KEY(id),
uid int unsigned NOT NULL DEFAULT '0',
title varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci ,
description varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci ,
preview_ext varchar(4),
folder varchar(32),
category smallint(5) unsigned NOT NULL DEFAULT '0',
creator varchar(30),
email varchar(30),
time_update datetime,
sign tinyint unsigned,
views mediumint unsigned,
hl_on boolean NOT NULL DEFAULT '0',
dl_on boolean NOT NULL DEFAULT '0',
vp_position varchar(30),
vp_orientation varchar(40),
ip_upload varchar(15),
ip_last_modify varchar(15)
);
		
DROP TABLE IF EXISTS `think_category`;
CREATE TABLE think_category 
(
  `cate_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `root_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_name` varchar(50) NOT NULL DEFAULT '',
  `cate_isbest` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cate_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_level` smallint(5) unsigned NOT NULL DEFAULT '1',
  `cate_keywords` varchar(100) NOT NULL DEFAULT '',
  `cate_description` varchar(255) NOT NULL DEFAULT '',
  `cate_arrparentid` varchar(255) NOT NULL DEFAULT '',
  `cate_childcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_postcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cate_id`),
  KEY `root_id` (`root_id`)
);
		
DROP TABLE IF EXISTS `think_ucenter_member`;
CREATE TABLE `think_ucenter_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NOT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户表';
		
DROP TABLE IF EXISTS `think_member`;
CREATE TABLE `think_member` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  PRIMARY KEY (`uid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员表';
		
DROP TABLE IF EXISTS `think_diy`;
CREATE TABLE `think_diy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'DIY ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0'COMMENT '用户 ID',
  `title` char(100) NOT NULL DEFAULT '' COMMENT '名称',
  `shared` text NOT NULL DEFAULT '' COMMENT '分享的模型',
  `basic` text NOT NULL DEFAULT '' COMMENT '分享的模型',
  `internet` text NOT NULL DEFAULT '' COMMENT '分享的模型',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='DIY表';";
if ($con->multi_query($sqls))
{
	echo "数据库安装完毕！！！";
	}else{

echo "数据库安装出错！！！";
	}
$con->close();
?>