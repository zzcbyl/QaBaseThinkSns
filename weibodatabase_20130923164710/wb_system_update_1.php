<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_system_update`;");
E_C("CREATE TABLE `wb_system_update` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `version` varchar(100) NOT NULL,
  `package` varchar(100) NOT NULL,
  `description` text,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>