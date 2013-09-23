<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_notify_email`;");
E_C("CREATE TABLE `wb_notify_email` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) NOT NULL COMMENT 'UiD',
  `node` varchar(50) NOT NULL COMMENT '节点名称',
  `appname` varchar(50) NOT NULL COMMENT '应用名称',
  `email` varchar(250) NOT NULL COMMENT '邮件接受地址',
  `is_send` tinyint(2) NOT NULL COMMENT '是否已经发送',
  `title` varchar(250) NOT NULL COMMENT '邮件标题',
  `body` text NOT NULL COMMENT '邮件内容',
  `ctime` int(11) NOT NULL COMMENT '添加时间',
  `sendtime` int(11) NOT NULL COMMENT '发送时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>