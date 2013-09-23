<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_denounce`;");
E_C("CREATE TABLE `wb_denounce` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `from` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '资源来源位置',
  `aid` int(10) NOT NULL COMMENT '资源ID',
  `state` tinyint(3) NOT NULL COMMENT '状态',
  `uid` int(10) NOT NULL COMMENT '举报人',
  `fuid` int(10) NOT NULL COMMENT '被举报人',
  `reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '举报原因',
  `content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '举报内容',
  `ctime` int(10) NOT NULL COMMENT '举报时间',
  `source_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '资源来源页面',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>