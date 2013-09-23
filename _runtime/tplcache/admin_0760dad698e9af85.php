<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo APPS_URL;?>/admin/_static/admin.css" rel="stylesheet" type="text/css">
<script>
/**
 * 全局变量
 */
var SITE_URL  = '<?php echo SITE_URL; ?>';
var THEME_URL = '__THEME__';
var APPNAME   = '<?php echo APP_NAME; ?>';
var UPLOAD_URL ='<?php echo UPLOAD_URL;?>';
var MID		  = '<?php echo $mid; ?>';
var UID		  = '<?php echo $uid; ?>';
// Js语言变量
var LANG = new Array();
</script>
<script type="text/javascript" src="__THEME__/js/jquery.js"></script>
<script type="text/javascript" src="__THEME__/js/core.js"></script>
<script src="__THEME__/js/module.js"></script>
<script src="__THEME__/js/common.js"></script>
<script src="__THEME__/js/module.common.js"></script>
<script src="__THEME__/js/module.weibo.js"></script>
<script type="text/javascript" src="<?php echo APPS_URL;?>/admin/_static/admin.js?t=11"></script>
<script type="text/javascript" src = "__THEME__/js/ui.core.js"></script>
<script type="text/javascript" src = "__THEME__/js/ui.draggable.js"></script>
<?php /* 非admin应用的后台js脚本统一写在  模板风格对应的app目录下的admin.js中*/
if(APP_NAME != 'admin' && file_exists(APP_PUBLIC_PATH.'/admin.js')){ ?>
<script type="text/javascript" src="<?php echo APP_PUBLIC_URL;?>/admin.js"></script>
<?php } ?>
<?php if(!empty($langJsList)) { ?>
<?php if(is_array($langJsList)): ?><?php $i = 0;?><?php $__LIST__ = $langJsList?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = ($i % 2 )?><script src="<?php echo ($vo); ?>"></script><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
<?php } ?>
</head>
<body>

<div id="container" class="so_main">
  <div class="page_tit">邮件邀请</div>
  <!-- START TAB框 -->
  <?php if(!empty($pageTab)): ?>
  <div class="tit_tab">
    <ul>
    <?php !$_REQUEST['tabHash'] && $_REQUEST['tabHash'] =  $pageTab[0]['tabHash']; ?>
    <?php if(is_array($pageTab)): ?><?php $i = 0;?><?php $__LIST__ = $pageTab?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$t): ?><?php ++$i;?><?php $mod = ($i % 2 )?><li><a href="<?php echo ($t["url"]); ?>&tabHash=<?php echo ($t["tabHash"]); ?>" <?php if($t['tabHash'] == $_REQUEST['tabHash']){ echo 'class="on"';} ?>><?php echo ($t["title"]); ?></a></li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
    </ul>
  </div>
  <?php endif; ?>
  <!-- END TAB框 -->
  <div class="form2">
    <div class="inviteMode">
      <div class="invite-title">
        <h4>输入邀请用户邮箱</h4>
        <input id="check_email" class="s-txt" type="text" autocomplete="off"><a href="javascript:;" onclick="doInviteEmail();" class="btn-green-big">发送邀请</a>
        <div id="prompt_box" class="box-ver"></div>
      </div>
      <!--
      <div class="invite-user-list">
        <h4>已邀请用户：</h4>
        <?php if(empty($inviteList)): ?>
        <p>当前无邀请好友</p>
        <?php else: ?>
        <ul>
          <li>
            <div class="w0">&nbsp;</div>
            <div class="w3">昵称</div>
            <div class="w1">邮箱</div>
            <div class="">邀请人</div>
           </li>
          <?php if(is_array($inviteList["data"])): ?><?php $i = 0;?><?php $__LIST__ = $inviteList["data"]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = ($i % 2 )?><li>
            <div class="w0"><a href="<?php echo ($vo["space_url"]); ?>"><img src="<?php echo ($vo["avatar_small"]); ?>" width="30" height="30"/></a></div>
            <div class="w3"><a href="<?php echo ($vo["space_url"]); ?>"><?php echo ($vo["uname"]); ?></a></div>
            <div class="w1"><a href="<?php echo ($vo["space_url"]); ?>"><?php echo ($vo["email"]); ?></a></div>
            <div class=""><a href="<?php echo ($vo["inviteInfo"]["space_url"]); ?>"><?php echo ($vo["inviteInfo"]["uname"]); ?></a></div>
          </li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
        </ul>
        <div class="page"><?php echo ($inviteList["html"]); ?></div>
        <?php endif; ?>
      </div>
      -->
  </div>
</div>

<script type="text/javascript">
/**
 * 发送邮件邀请
 */
var doInviteEmail = function()
{
  // 邮件内容
  var email = $.trim($('#check_email').val());
  // 测试邮件内容
  if(email == '') {
    ui.error('请填写邀请人邮箱');
    return false;
  }
  // 提交邀请
  $.post(U('admin/Config/doInvite'), {email:email}, function(res) {
    if(res.status ) {
      ui.success(res.info);
      setTimeout(function() {
        location.href = location.href;
      }, 1500);
      return false;
    } else {
      ui.error(res.info);
      return false;
    }
  }, 'json');
  return false;
};
</script>
<?php if(!empty($onload)){ ?>
<script type="text/javascript">
/**
 * 初始化对象
 */
//表格样式
$(document).ready(function(){
    <?php foreach($onload as $v){ echo $v,';';} ?>
});
</script>
<?php } ?>
</body>
</html>