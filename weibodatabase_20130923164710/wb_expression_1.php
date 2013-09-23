<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_expression`;");
E_C("CREATE TABLE `wb_expression` (
  `expression_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `type` varchar(255) NOT NULL DEFAULT 'miniblog',
  `emotion` varchar(255) NOT NULL COMMENT '文本编码，格式：[文本]，如[拥抱]、[示爱]、[呲牙]等',
  `filename` varchar(255) NOT NULL COMMENT '表情图片文件名',
  PRIMARY KEY (`expression_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8");
E_D("replace into `wb_expression` values('1','拥抱','miniblog','[拥抱]','hug.gif');");
E_D("replace into `wb_expression` values('2','示爱','miniblog','[示爱]','kiss.gif');");
E_D("replace into `wb_expression` values('3','呲牙','miniblog','[呲牙]','lol.gif');");
E_D("replace into `wb_expression` values('4','可爱','miniblog','[可爱]','loveliness.gif');");
E_D("replace into `wb_expression` values('5','折磨','miniblog','[折磨]','mad.gif');");
E_D("replace into `wb_expression` values('6','难过','miniblog','[难过]','sad.gif');");
E_D("replace into `wb_expression` values('7','流汗','miniblog','[流汗]','sweat.gif');");
E_D("replace into `wb_expression` values('8','憨笑','miniblog','[憨笑]','biggrin.gif');");
E_D("replace into `wb_expression` values('9','大哭','miniblog','[大哭]','cry.gif');");
E_D("replace into `wb_expression` values('11','握手','miniblog','[握手]','handshake.gif');");
E_D("replace into `wb_expression` values('12','发怒','miniblog','[发怒]','huffy.gif');");
E_D("replace into `wb_expression` values('13','惊讶','miniblog','[惊讶]','shocked.gif');");
E_D("replace into `wb_expression` values('14','害羞','miniblog','[害羞]','shy.gif');");
E_D("replace into `wb_expression` values('15','微笑','miniblog','[微笑]','smile.gif');");
E_D("replace into `wb_expression` values('16','偷笑','miniblog','[偷笑]','titter.gif');");
E_D("replace into `wb_expression` values('17','调皮','miniblog','[调皮]','tongue.gif');");
E_D("replace into `wb_expression` values('19','啤酒','miniblog','[啤酒]','beer.gif');");
E_D("replace into `wb_expression` values('20','蛋糕','miniblog','[蛋糕]','cake.gif');");
E_D("replace into `wb_expression` values('21','奋斗','miniblog','[奋斗]','fendou.gif');");
E_D("replace into `wb_expression` values('22','出错了','miniblog','[出错了]','funk.gif');");
E_D("replace into `wb_expression` values('23','哈欠','miniblog','[哈欠]','ha.gif');");
E_D("replace into `wb_expression` values('24','晚安','miniblog','[晚安]','moon.gif');");
E_D("replace into `wb_expression` values('25','欧克','miniblog','[欧克]','ok.gif');");
E_D("replace into `wb_expression` values('26','猪头','miniblog','[猪头]','pig.gif');");
E_D("replace into `wb_expression` values('31','晕','miniblog','[晕]','yun.gif');");
E_D("replace into `wb_expression` values('32','偶也 ','miniblog','[偶也]','victory.gif');");

require("../../inc/footer.php");
?>