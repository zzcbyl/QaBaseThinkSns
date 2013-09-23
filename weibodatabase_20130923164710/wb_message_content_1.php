<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_message_content`;");
E_C("CREATE TABLE `wb_message_content` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '私信内对话ID',
  `list_id` int(11) unsigned NOT NULL COMMENT '私信ID',
  `from_uid` int(11) unsigned NOT NULL COMMENT '会话发布者UID',
  `content` text COMMENT '会话内容',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除，0：否；1：是',
  `mtime` int(11) unsigned NOT NULL COMMENT '会话发布时间',
  PRIMARY KEY (`message_id`),
  KEY `list_id` (`list_id`,`is_del`,`mtime`),
  KEY `list_id_2` (`list_id`,`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>