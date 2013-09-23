<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_group`;");
E_C("CREATE TABLE `wb_user_group` (
  `user_group_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_group_name` varchar(255) NOT NULL COMMENT '用户组名称',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `user_group_icon` varchar(120) NOT NULL COMMENT '用户组图标名称',
  `user_group_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '组类型、0：普通组，1:特殊组，',
  `app_name` varchar(20) NOT NULL DEFAULT 'public' COMMENT '应用名称',
  `is_authenticate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为认证组',
  PRIMARY KEY (`user_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8");
E_D("replace into `wb_user_group` values('1','管理员','1354605105','-1','0','public','0');");
E_D("replace into `wb_user_group` values('2','巡逻员','1363097759','v_02.gif','0','public','0');");
E_D("replace into `wb_user_group` values('3','正常用户','1354605704','-1','0','public','0');");
E_D("replace into `wb_user_group` values('4','禁言用户','1354605046','v_04.png','0','public','0');");
E_D("replace into `wb_user_group` values('5','个人认证','1350012209','v_01.gif','0','public','1');");
E_D("replace into `wb_user_group` values('6','企业/组织认证','1350012483','v_06.gif','0','public','1');");
E_D("replace into `wb_user_group` values('7','达人用户','1354605062','v_01.png','0','public','1');");

require("../../inc/footer.php");
?>