<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_change_style`;");
E_C("CREATE TABLE `wb_user_change_style` (
  `uid` int(11) unsigned NOT NULL COMMENT '户用UID',
  `classname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '皮肤的样式表名称',
  `background` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '肤的皮背景图片地址',
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>