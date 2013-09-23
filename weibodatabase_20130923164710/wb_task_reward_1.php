<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_task_reward`;");
E_C("CREATE TABLE `wb_task_reward` (
  `task_type` int(11) DEFAULT NULL COMMENT '任务类型',
  `task_level` int(11) DEFAULT NULL COMMENT '任务等级',
  `reward` varchar(255) DEFAULT NULL COMMENT '任务奖励',
  UNIQUE KEY `index_type_level` (`task_type`,`task_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
E_D("replace into `wb_task_reward` values('2','1','{\"exp\":50,\"score\":50,\"medal\":{\"id\":70,\"name\":\"\\\\u65b0\\\\u624b\\\\u4e0a\\\\u8def\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcb0d64a404.png\"}}');");
E_D("replace into `wb_task_reward` values('1','1','{\"exp\":10,\"score\":10,\"medal\":null}');");
E_D("replace into `wb_task_reward` values('2','5','{\"exp\":1000,\"score\":1000,\"medal\":{\"id\":74,\"name\":\"\\\\u72ec\\\\u5b64\\\\u6c42\\\\u8d25\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcb0426d7df.png\"}}');");
E_D("replace into `wb_task_reward` values('2','2','{\"exp\":100,\"score\":100,\"medal\":{\"id\":71,\"name\":\"\\\\u5c0f\\\\u6709\\\\u8fdb\\\\u6b65\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcb0c2a4fa2.png\"}}');");
E_D("replace into `wb_task_reward` values('2','3','{\"exp\":200,\"score\":200,\"medal\":{\"id\":72,\"name\":\"\\\\u5fae\\\\u535a\\\\u8fbe\\\\u4eba\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcb08b08634.png\"}}');");
E_D("replace into `wb_task_reward` values('2','4','{\"exp\":400,\"score\":400,\"medal\":{\"id\":73,\"name\":\"\\\\u6b66\\\\u6797\\\\u9ad8\\\\u624b\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcb062ed874.png\"}}');");

require("../../inc/footer.php");
?>