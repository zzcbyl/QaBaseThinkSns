<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_navi`;");
E_C("CREATE TABLE `wb_navi` (
  `navi_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '导航ID',
  `navi_name` varchar(255) DEFAULT NULL COMMENT '导航名称',
  `app_name` varchar(255) DEFAULT NULL COMMENT '应用标志，如index、home、public等',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `target` varchar(10) DEFAULT NULL COMMENT '打开方式',
  `status` int(1) DEFAULT NULL COMMENT '状态（0关闭，1开启）',
  `position` varchar(10) DEFAULT NULL COMMENT '导航位置',
  `guest` int(1) DEFAULT NULL COMMENT '是否游客可见（0否，1是，默认1）',
  `is_app_navi` int(1) DEFAULT NULL COMMENT '是否应用内部导航 （0否，1是，默认1）',
  `parent_id` int(11) DEFAULT NULL COMMENT '（父导航，默认为0）',
  `order_sort` int(11) DEFAULT NULL COMMENT '应用排序 默认255',
  PRIMARY KEY (`navi_id`),
  KEY `status_postion` (`status`,`position`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8");
E_D("replace into `wb_navi` values('1','首页','public','{website}','_self','1','0','1','0','0','1');");
E_D("replace into `wb_navi` values('2','频道','channel','{website}/index.php?app=channel&mod=Index&act=index','_self','1','0','1','0','0','3');");
E_D("replace into `wb_navi` values('3','找人','people','{website}/index.php?app=people&mod=Index&act=index','_self','1','0','1','0','0','2');");
E_D("replace into `wb_navi` values('4','微吧','weiba','{website}/index.php?app=weiba&mod=Index&act=index','_self','1','0','1','0','0','4');");

require("../../inc/footer.php");
?>