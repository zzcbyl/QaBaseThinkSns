<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_credit_setting`;");
E_C("CREATE TABLE `wb_credit_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '积分动作',
  `alias` varchar(255) NOT NULL COMMENT '积分名称',
  `type` varchar(30) NOT NULL DEFAULT 'user' COMMENT '积分类型',
  `info` text NOT NULL COMMENT '积分说明',
  `score` int(11) DEFAULT NULL COMMENT '积分值',
  `experience` int(11) DEFAULT NULL COMMENT '经验值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=262 DEFAULT CHARSET=utf8");
E_D("replace into `wb_credit_setting` values('37','invite_friend','邀请好友','register','{action}{sign}了{score}{typecn}','8','2');");
E_D("replace into `wb_credit_setting` values('39','add_weibo','发布微博','weibo','{action}{sign}了{score}{typecn}','5','5');");
E_D("replace into `wb_credit_setting` values('40','user_login','用户登录','user','{action}{sign}了{score}{typecn}','2','1');");
E_D("replace into `wb_credit_setting` values('42','space_visited','空间被访问','user','{action}{sign}了{score}{typecn}','2','1');");
E_D("replace into `wb_credit_setting` values('92','init_default','注册积分','register','{action}{sign}了{score}{typecn}','200','0');");
E_D("replace into `wb_credit_setting` values('59','add_comment','评论别人','comment','{action}{sign}了{score}{typecn}','6','4');");
E_D("replace into `wb_credit_setting` values('60','reply_comment','回复别人的评论','comment','{action}{sign}了{score}{typecn}','2','1');");
E_D("replace into `wb_credit_setting` values('61','replied_comment','评论被回复','comment','{action}{sign}了{score}{typecn}','3','1');");
E_D("replace into `wb_credit_setting` values('63','reply_weibo','回复微博','weibo','{action}{sign}了{score}{typecn}','3','2');");
E_D("replace into `wb_credit_setting` values('64','commented_weibo','微博被评论','weibo','{action}{sign}了{score}{typecn}','2','3');");
E_D("replace into `wb_credit_setting` values('81','is_commented','被别人评论','comment','{action}{sign}了{score}{typecn}','2','1');");
E_D("replace into `wb_credit_setting` values('83','share_to_weibo','分享到微博','weibo','{action}{sign}了{score}{typecn}','4','1');");
E_D("replace into `wb_credit_setting` values('88','delete_comment','删除评论','comment','{action}{sign}了{score}{typecn}','-3','1');");
E_D("replace into `wb_credit_setting` values('89','delete_weibo','删除微博','weibo','{action}{sign}了{score}{typecn}','-1','1');");
E_D("replace into `wb_credit_setting` values('90','forward_weibo','转发微博','weibo','{action}{sign}了{score}{typecn}','1','2');");
E_D("replace into `wb_credit_setting` values('91','forwarded_weibo','微博被转发','weibo','{action}{sign}了{score}{typecn}','3','2');");
E_D("replace into `wb_credit_setting` values('93','delete_weibo_comment','删除微博评论','weibo','{action}{sign}了{score}{typecn}','-2','1');");
E_D("replace into `wb_credit_setting` values('94','add_medal','获得勋章','medal','','6','2');");
E_D("replace into `wb_credit_setting` values('103','delete_medal','丢失勋章','medal','{action}{sign}了{score}{typecn}','-5','0');");
E_D("replace into `wb_credit_setting` values('179','core_code','申请邀请码','register','','-1','-2');");
E_D("replace into `wb_credit_setting` values('185','user_login','用户登录','core','','1','1');");
E_D("replace into `wb_credit_setting` values('186','space_access','空间被访问','core','','2','1');");
E_D("replace into `wb_credit_setting` values('187','comment_weibo','评论微博','weibo','','3','2');");
E_D("replace into `wb_credit_setting` values('188','collect_weibo','收藏微博','weibo','','1','1');");
E_D("replace into `wb_credit_setting` values('189','report_weibo','举报微博','weibo','','1','1');");
E_D("replace into `wb_credit_setting` values('190','collected_weibo','微博被收藏','weibo','','1','1');");
E_D("replace into `wb_credit_setting` values('191','reported_weibo','微博被举报','weibo','','0','0');");
E_D("replace into `wb_credit_setting` values('192','recommend_to_channel','推荐至频道','channel','','1','1');");
E_D("replace into `wb_credit_setting` values('193','unrecommend_to_channel','取消推荐至频道','channel','','1','1');");
E_D("replace into `wb_credit_setting` values('194','publish_topic','发表帖子','weiba','','10','10');");
E_D("replace into `wb_credit_setting` values('195','forward_topic','转发帖子','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('196','comment_topic','评论帖子','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('197','collect_topic','收藏帖子','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('198','top_topic_all','帖子被设置全局置顶','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('199','top_topic_weiba','帖子被设置吧内置顶','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('200','dist_topic','帖子被设置精华','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('201','undist_topic','帖子被取消精华','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('202','untop_topic_all','帖子被取消全局置顶','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('203','untop_topic_weiba','帖子被取消吧内置顶','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('204','forwarded_topic','帖子被转发','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('205','commented_topic','帖子被评论','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('206','collected_topic','帖子被收藏','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('207','recommend_topic','帖子被推荐','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('208','delete_topic','删除帖子','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('209','delete_topic_comment','删除帖子回复','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('210','follow_weiba','关注微吧','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('211','unfollow_weiba','取消关注微吧','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('212','out_weiba','踢出微吧','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('213','appointed_weiba','被任命为吧主','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('214','unappointed_weiba','取消任命吧主','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('215','recommended_weiba','微吧被推荐','weiba','','0','0');");
E_D("replace into `wb_credit_setting` values('260','digg_weibo','顶微博','weibo','','1','1');");
E_D("replace into `wb_credit_setting` values('261','digged_weibo','微博被顶','weibo','','6','5');");

require("../../inc/footer.php");
?>