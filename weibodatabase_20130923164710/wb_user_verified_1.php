<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_verified`;");
E_C("CREATE TABLE `wb_user_verified` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(11) unsigned NOT NULL COMMENT '户用UID',
  `usergroup_id` int(11) NOT NULL COMMENT '认证类型，即所申请的认证组的ID',
  `user_verified_category_id` int(11) NOT NULL DEFAULT '0' COMMENT '认证分类ID',
  `company` varchar(255) NOT NULL COMMENT '公司名称',
  `realname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '真实姓名',
  `idcard` varchar(50) NOT NULL COMMENT '证件号码',
  `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '联系方式',
  `info` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '证认信息',
  `verified` tinyint(2) NOT NULL DEFAULT '0' COMMENT '认证状态，0：否；1：是',
  `attach_id` varchar(255) NOT NULL COMMENT '认证资料，存储用户上传的ID',
  `reason` varchar(255) DEFAULT NULL COMMENT '证认理由',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>