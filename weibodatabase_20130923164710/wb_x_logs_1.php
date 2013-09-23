<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_x_logs`;");
E_C("CREATE TABLE `wb_x_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `uname` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '帐号\r\n',
  `app_name` char(80) NOT NULL COMMENT '日志所属应用',
  `group` char(80) DEFAULT NULL COMMENT '日志分组',
  `action` char(80) NOT NULL COMMENT '日志行为',
  `ip` varchar(80) DEFAULT NULL COMMENT 'IP地址',
  `data` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '序列化保存的模板变量',
  `url` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '记录日志时的URL地址',
  `ctime` int(11) NOT NULL COMMENT '创建时间',
  `isAdmin` tinyint(2) NOT NULL COMMENT '是否是管理员日志',
  `keyword` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '模板变量值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>