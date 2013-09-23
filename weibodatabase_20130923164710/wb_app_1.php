<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_app`;");
E_C("CREATE TABLE `wb_app` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) NOT NULL COMMENT 'app名称',
  `app_alias` varchar(255) NOT NULL COMMENT 'app别名',
  `description` text COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:''关闭'',1:开启',
  `host_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0，1',
  `app_entry` varchar(255) DEFAULT NULL COMMENT '前台入口，例：''Index/index''',
  `icon_url` varchar(255) DEFAULT NULL COMMENT '图标',
  `large_icon_url` varchar(255) DEFAULT NULL COMMENT '图标地址',
  `admin_entry` varchar(255) DEFAULT NULL COMMENT '后台管理地址',
  `statistics_entry` varchar(255) DEFAULT NULL COMMENT '接口地址',
  `display_order` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `ctime` int(11) DEFAULT NULL COMMENT '安装时间戳',
  `version` varchar(255) DEFAULT NULL COMMENT '版本号',
  `api_key` varchar(255) DEFAULT NULL COMMENT '用户api_key',
  `secure_key` varchar(255) DEFAULT NULL COMMENT 'API密钥',
  `company_name` varchar(255) NOT NULL COMMENT '公司名称',
  `has_mobile` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否有移动客户端.0:无，1有',
  `child_menu` text NOT NULL COMMENT '子导航数据',
  `add_front_top` tinyint(1) DEFAULT '1',
  `add_front_applist` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`app_id`),
  KEY `app_name` (`app_name`),
  KEY `status_id` (`app_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8");
E_D("replace into `wb_app` values('5','channel','频道','频道','1','0','Index/index','','','channel/Admin/index','Statistics/statistics','5','1352520034','','','','智士软件','1','a:1:{s:7:\"channel\";a:2:{s:3:\"url\";s:19:\"channel/Index/index\";s:6:\"public\";i:0;}}','1','1');");
E_D("replace into `wb_app` values('3','weiba','微吧','微吧','1','0','Index/index','','','weiba/Admin/index','Statistics/statistics','3','1352256442','1.0','','','智士软件','1','a:1:{s:5:\"weiba\";a:2:{s:3:\"url\";s:19:\"weiba/Index/myWeiba\";s:6:\"public\";i:0;}}','1','1');");
E_D("replace into `wb_app` values('6','people','找人','按各种维度展示用户','1','0','Index/index','','','','','6','1363096393','','','','智士软件','0','a:0:{}','1','1');");

require("../../inc/footer.php");
?>