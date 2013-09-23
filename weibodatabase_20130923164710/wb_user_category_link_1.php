<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_category_link`;");
E_C("CREATE TABLE `wb_user_category_link` (
  `user_category_link_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户分类关联表ID - 主键',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `user_category_id` int(11) NOT NULL COMMENT '用户分类ID',
  `sort` int(11) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`user_category_link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>