<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_notify_node`;");
E_C("CREATE TABLE `wb_notify_node` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `node` varchar(50) NOT NULL COMMENT '节点名称',
  `nodeinfo` varchar(50) NOT NULL COMMENT '节点描述',
  `appname` varchar(50) NOT NULL COMMENT '应用名称',
  `content_key` varchar(50) NOT NULL COMMENT '内容key',
  `title_key` varchar(50) NOT NULL COMMENT '标题key',
  `send_email` tinyint(2) NOT NULL COMMENT '是否发送邮件',
  `send_message` tinyint(2) NOT NULL COMMENT '是否发送消息',
  `type` tinyint(2) NOT NULL COMMENT '信息类型：1 表示用户发送的 2表示是系统发送的',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=utf8");
E_D("replace into `wb_notify_node` values('1','register_active','注册激活','public','NOTIFY_REGISTER_ACTIVE_CONTENT','NOTIFY_REGISTER_ACTIVE_TITLE','1','0','1');");
E_D("replace into `wb_notify_node` values('30','register_audit','注册审核','public','NOTIFY_REGISTER_AUDIT_CONTENT','NOTIFY_REGISTER_AUDIT_TITLE','1','0','2');");
E_D("replace into `wb_notify_node` values('31','verify_audit','认证审核','public','NOTIFY_VERIFY_AUDIT_CONTENT','NOTIFY_VERIFY_AUDIT_TITLE','0','1','2');");
E_D("replace into `wb_notify_node` values('32','denouce_audit','举报审核','public','NOTIFY_DENOUCE_AUDIT_CONTENT','NOTIFY_DENOUCE_AUDIT_TITLE','1','0','2');");
E_D("replace into `wb_notify_node` values('3','audit_ok','通过审核','public','NOTIFY_AUDIT_OK_CONTENT','NOTIFY_AUDIT_OK_TITLE','1','0','1');");
E_D("replace into `wb_notify_node` values('4','password_reset','密码重置','public','NOTIFY_PASSWORD_RESET_CONTENT','NOTIFY_PASSWORD_RESET_TITLE','1','0','1');");
E_D("replace into `wb_notify_node` values('5','password_setok','密码重置成功','public','NOTIFY_PASSWORD_SETOK_CONTENT','NOTIFY_PASSWORD_SETOK_TITLE','1','0','1');");
E_D("replace into `wb_notify_node` values('6','user_lock','帐号锁定','public','NOTIFY_USER_LOCK_CONTENT','NOTIFY_USER_LOCK_TITLE','1','0','1');");
E_D("replace into `wb_notify_node` values('7','atme','提到我的','public','NOTIFY_ATME_CONTENT','NOTIFY_ATME_TITLE','0','0','1');");
E_D("replace into `wb_notify_node` values('8','comment','评论我的','public','NOTIFY_COMMENT_CONTENT','NOTIFY_COMMENT_TITLE','0','0','1');");
E_D("replace into `wb_notify_node` values('9','new_message','收到私信','public','NOTIFY_NEW_MESSAGE_CONTENT','NOTIFY_NEW_MESSAGE_TITLE','0','0','1');");
E_D("replace into `wb_notify_node` values('11','register_invate','邀请注册','public','NOTIFY_REGISTER_INVATE_CONTENT','NOTIFY_REGISTER_INVATE_TITLE','1','0','1');");
E_D("replace into `wb_notify_node` values('12','register_invate_ok','邀请注册成功','public','NOTIFY_REGISTER_INVATE_OK_CONTENT','NOTIFY_REGISTER_INVATE_OK_TITLE','1','0','1');");
E_D("replace into `wb_notify_node` values('33','feedback_audit','意见反馈审核','public','NOTIFY_FEEDBACK_AUDIT_CONTENT','NOTIFY_FEEDBACK_AUDIT_TITLE','1','0','2');");
E_D("replace into `wb_notify_node` values('29','public_account_delverify','注销认证','public','NOTIFY_DEL_VERIFY_CONTENT','NOTIFY_DEL_VERIFY_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('28','admin_user_doverify_reject','后台认证驳回','public','NOTIFY_AUTHENTICATE_DOVERIFY_REJECT_CONTENT','NOTIFY_AUTHENTICATE_DOVERIFY_REJECT_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('26','public_account_doAuthenticate','申请认证','public','NOTIFY_AUTHENTICATE_SUBMIT_CONTENT','NOTIFY_AUTHENTICATE_SUBMIT_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('27','admin_user_doverify_ok','后台认证通过','public','NOTIFY_AUTHENTICATE_DOVERIFY_OK_CONTENT','NOTIFY_AUTHENTICATE_DOVERIFY_OK_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('41','weiba_apply_reject','驳回吧主申请','weiba','NOTIFY_WEIBA_APPLY_REJECT_CONTENT','NOTIFY_WEIBA_APPLY_REJECT_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('40','weiba_apply_ok','通过吧主申请','weiba','NOTIFY_WEIBA_APPLY_OK_CONTENT','NOTIFY_WEIBA_APPLY_OK_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('39','weiba_apply','申请吧主','weiba','NOTIFY_WEIBA_APPLY_CONTENT','NOTIFY_WEIBA_APPLY_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('42','admin_add_user_medal','颁发勋章','public','NOTIFY_ADD_USER_MEDAL_CONTENT','NOTIFY_ADD_USER_MEDAL_TITLE','0','1','2');");
E_D("replace into `wb_notify_node` values('43','weiba_post_set','设置帖子','weiba','NOTIFY_WEIBA_POST_SET_CONTENT','NOTIFY_WEIBA_POST_SET_TITLE','0','1','2');");
E_D("replace into `wb_notify_node` values('44','channel_add_feed','推荐到频道','channel','NOTIFY_CHANNEL_ADD_FEED_CONTENT','NOTIFY_CHANNEL_ADD_FEED_TITLE','0','1','2');");
E_D("replace into `wb_notify_node` values('45','channel_audit','频道投稿审核通过','channel','NOTIFY_CHANNEL_AUDIT_CONTENT','NOTIFY_CHANNEL_AUDIT_TITLE','0','1','2');");
E_D("replace into `wb_notify_node` values('46','feed_audit','微博审核','public','NOTIFY_FEED_AUDIT_CONTENT','NOTIFY_FEED_AUDIT_TITLE','0','1','2');");
E_D("replace into `wb_notify_node` values('47','comment_audit','评论审核','public','NOTIFY_COMMENT_AUDIT_CONTENT','NOTIFY_COMMENT_AUDIT_TITLE','0','1','2');");
E_D("replace into `wb_notify_node` values('48','register_welcome','注册欢迎','public','NOTIFY_REGISTER_WELCOME_CONTENT','NOTIFY_REGISTER_WELCOME_TITLE','1','1','1');");
E_D("replace into `wb_notify_node` values('52','tipoff_bonus','爆料发奖','tipoff','NOTIFY_TIPOFF_BONUS_CONTENT','NOTIFY_TIPOFF_BONUS_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('51','tipoff_deal','爆料处理提醒','tipoff','NOTIFY_TIPOFF_DEAL_CONTENT','NOTIFY_TIPOFF_DEAL_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('56','gift_send','赠送礼物','gift','NOTIFY_GIFT_SEND_CONTENT','NOTIFY_GIFT_SEND_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('54','sitelist_approve','站点通过','develop','NOTIFY_SITELIST_APPROVE_CONTENT','NOTIFY_SITELIST_APPROVE_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('55','sitelist_deny','站点拒绝','develop','NOTIFY_SITELIST_DENY_CONTENT','NOTIFY_SITELIST_DENY_TITLE','0','1','1');");
E_D("replace into `wb_notify_node` values('64','digg','微博的赞','public','digg_message_content','digg_message_title','0','1','1');");

require("../../inc/footer.php");
?>