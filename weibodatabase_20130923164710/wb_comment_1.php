<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_comment`;");
E_C("CREATE TABLE `wb_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，评论编号',
  `app` char(15) NOT NULL COMMENT '所属应用',
  `table` char(15) NOT NULL COMMENT '被评论的内容所存储的表',
  `row_id` int(11) NOT NULL COMMENT '应用进行评论的内容的编号',
  `app_uid` int(11) NOT NULL DEFAULT '0' COMMENT '应用内进行评论的内容的作者的编号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '评论者编号',
  `content` text NOT NULL COMMENT '评论内容',
  `to_comment_id` int(11) NOT NULL DEFAULT '0' COMMENT '被回复的评论的编号',
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '被回复的评论的作者的编号',
  `data` text NOT NULL COMMENT '所评论的内容的相关参数（序列化存储）',
  `ctime` int(11) NOT NULL COMMENT '评论发布的时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '标记删除（0：没删除，1：已删除）',
  `client_type` tinyint(2) NOT NULL COMMENT '客户端类型，0：网站；1：手机网页版；2：android；3：iphone',
  `is_audit` tinyint(1) DEFAULT '1' COMMENT '是否已审核 0-未审核 1-已审核',
  `storey` int(11) DEFAULT '0' COMMENT '评论绝对楼层',
  PRIMARY KEY (`comment_id`),
  KEY `app` (`table`,`is_del`,`row_id`),
  KEY `app_3` (`app_uid`,`to_uid`,`is_del`,`table`),
  KEY `app_2` (`uid`,`is_del`,`table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>