<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_x_article`;");
E_C("CREATE TABLE `wb_x_article` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '标题',
  `uid` int(10) NOT NULL COMMENT '发布者ID',
  `mtime` int(11) NOT NULL COMMENT '修改时间',
  `sort` tinyint(5) NOT NULL COMMENT '排序',
  `content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '内容',
  `attach` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '附件信息',
  `type` tinyint(3) NOT NULL COMMENT '类型:1公告，2页脚配置文章',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>