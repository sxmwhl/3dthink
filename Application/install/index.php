<?php
header("Content-Type: text/html;charset=utf-8"); 
$con= new mysqli("localhost","root","");
// Create database
if ($con->query("drop database if exists `3dshare`")){
	echo "database delete";
}else {
	echo "delete error";
}
if ($con->query("CREATE DATABASE if not exists `3dshare` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"))
  {
  echo "Database created";
  }
else
  {
  echo "Error creating database: " . mysql_error();
  }
// Create table in 3dshare database
$con->select_db("3dshare");
$sqls = "
CREATE TABLE think_moxing 
(
id int unsigned NOT NULL AUTO_INCREMENT, 
PRIMARY KEY(id),
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
CREATE TABLE think_category 
(
  `cate_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `root_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_name` varchar(50) NOT NULL DEFAULT '',
  `cate_isbest` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cate_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_keywords` varchar(100) NOT NULL DEFAULT '',
  `cate_description` varchar(255) NOT NULL DEFAULT '',
  `cate_arrparentid` varchar(255) NOT NULL DEFAULT '',
  `cate_childcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_postcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cate_id`),
  KEY `root_id` (`root_id`)
);";
if ($con->multi_query($sqls))
{
	echo "数据库安装完毕！！！";
	}else{

echo "数据库安装出错！！！";
	}
$con->close();
?>