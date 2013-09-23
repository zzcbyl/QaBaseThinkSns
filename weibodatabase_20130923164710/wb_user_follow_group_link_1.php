<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_follow_group_link`;");
E_C("CREATE TABLE `wb_user_follow_group_link` (
  `follow_group_link_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关注组联表ID',
  `follow_group_id` int(11) NOT NULL COMMENT '关注组ID',
  `follow_id` int(11) NOT NULL COMMENT 'user_follow  表中follow_id',
  `fid` int(11) NOT NULL COMMENT '被关注人ID',
  `uid` int(11) NOT NULL COMMENT '关注人ID',
  PRIMARY KEY (`follow_group_link_id`),
  UNIQUE KEY `follow_group_id` (`uid`,`fid`,`follow_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>