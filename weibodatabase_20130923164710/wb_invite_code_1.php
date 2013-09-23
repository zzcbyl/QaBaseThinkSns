<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_invite_code`;");
E_C("CREATE TABLE `wb_invite_code` (
  `invite_code_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `inviter_uid` int(11) unsigned NOT NULL COMMENT '邀请人UID',
  `code` varchar(120) NOT NULL COMMENT '邀请码',
  `is_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已使用',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为管理员邀请',
  `type` char(40) NOT NULL COMMENT '邀请码类型',
  `is_received` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已接受邀请',
  `receiver_uid` int(11) NOT NULL DEFAULT '0' COMMENT '邀请人UID',
  `receiver_email` varchar(50) DEFAULT NULL COMMENT '邀请人注册邮箱',
  `ctime` int(11) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`invite_code_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8");
E_D("replace into `wb_invite_code` values('1','1','c487de5823de3af1','0','1','email','0','0',NULL,'0');");

require("../../inc/footer.php");
?>