<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_privacy`;");
E_C("CREATE TABLE `wb_user_privacy` (
  `uid` int(11) NOT NULL COMMENT '户用UID',
  `key` varchar(120) NOT NULL COMMENT '配置键名，如weibo_comment（评论）,message（私信）',
  `value` varchar(120) NOT NULL COMMENT '配置值，0：所有人(不包括你的黑名单用户)；1：我关注的人'
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>