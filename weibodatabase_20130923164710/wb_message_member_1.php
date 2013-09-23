<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_message_member`;");
E_C("CREATE TABLE `wb_message_member` (
  `list_id` int(11) unsigned NOT NULL COMMENT '私信ID',
  `member_uid` int(11) unsigned NOT NULL COMMENT '参与私信的用户UID',
  `new` smallint(8) unsigned NOT NULL DEFAULT '0' COMMENT '未读消息数',
  `message_num` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '消息总数',
  `ctime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '该参与者最后会话时间',
  `list_ctime` int(11) unsigned NOT NULL COMMENT '私信最后会话时间',
  `is_del` int(11) NOT NULL COMMENT '是否删除（假的删除）',
  PRIMARY KEY (`list_id`,`member_uid`),
  KEY `new` (`new`),
  KEY `ctime` (`ctime`),
  KEY `list_ctime` (`list_ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>