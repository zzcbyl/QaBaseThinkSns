<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_profile`;");
E_C("CREATE TABLE `wb_user_profile` (
  `uid` int(11) unsigned NOT NULL COMMENT '户用UID',
  `field_id` smallint(8) unsigned NOT NULL COMMENT '数据资料ID',
  `field_data` text NOT NULL COMMENT '资料数据字段名',
  `privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：所有人，1：好友， 2：互相关注，3：关注我的，4：我关注的',
  UNIQUE KEY `uid` (`uid`,`field_id`),
  KEY `uid_2` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>