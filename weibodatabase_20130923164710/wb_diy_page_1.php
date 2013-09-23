<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_diy_page`;");
E_C("CREATE TABLE `wb_diy_page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) NOT NULL COMMENT '页面域名',
  `page_name` varchar(30) NOT NULL COMMENT '页面名称',
  `layout_data` text COMMENT '页面布局数据',
  `widget_data` text COMMENT '页面里面模块数据',
  `canvas` varchar(255) DEFAULT NULL COMMENT '应用画布',
  `lock` tinyint(1) DEFAULT '0' COMMENT '是否锁定不可以删除',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否开放用户访问',
  `guest` tinyint(1) DEFAULT '1' COMMENT '游客是否可以访问',
  `visit_count` int(11) unsigned DEFAULT '0' COMMENT '浏览次数',
  `uid` int(11) DEFAULT NULL COMMENT '用户UID',
  `manager` varchar(255) DEFAULT '' COMMENT '管理员',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `seo_title` varchar(255) DEFAULT NULL COMMENT '页面seo标题',
  `seo_keywords` varchar(255) DEFAULT NULL COMMENT '页面seo关键字',
  `seo_description` varchar(500) DEFAULT NULL COMMENT '页面seo简介',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>