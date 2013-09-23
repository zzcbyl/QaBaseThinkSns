<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_feed_topic_link`;");
E_C("CREATE TABLE `wb_feed_topic_link` (
  `feed_topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL COMMENT '动态ID',
  `topic_id` int(11) NOT NULL COMMENT '话题ID',
  `type` varchar(255) NOT NULL DEFAULT '0' COMMENT '动态类型ID',
  PRIMARY KEY (`feed_topic_id`),
  KEY `topic_type` (`topic_id`,`type`),
  KEY `weibo` (`feed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>