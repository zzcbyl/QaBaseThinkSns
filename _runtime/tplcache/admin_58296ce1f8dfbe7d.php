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
  <div class="page_tit">链接邀请</div>
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
  <div class="from2">
    <div class="inviteMode">
      <div class="invite-title"><h4>每个邀请码可以邀请1个用户</h4></div>
      <div class="invite-links clearfix">
      	<a href="javascript:;" onclick="getInviteCode()">点击获取邀请码</a>
        <ul id="code_list">
          <?php if(is_array($codeList["data"])): ?><?php $i = 0;?><?php $__LIST__ = $codeList["data"]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = ($i % 2 )?><li>
            <div class="left">
              <input type="text"  class="text" style="width:450px;" onfocus="this.className='text2'" onblur="this.className='text'"  name="intro[<?php echo ($vo["field"]); ?>]" value="<?php echo SITE_URL;?>/index.php?invite=<?php echo ($vo["code"]); ?>"/>
            </div>
            <div class="left ml10">
              <?php if($vo['is_used'] == 1): ?>
              <span>已使用</span>
              <?php else: ?>
              <embed width="62" height="24" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" allowscriptaccess="sameDomain" wmode="transparent" quality="high" src="__APP__/copy.swf" flashvars="txt=<?php echo SITE_URL;?>/index.php?invite=<?php echo ($vo["code"]); ?>">
              <?php endif; ?>
            </div>
          </li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
        </ul>
      </div>
      <div class="Toolbar_inbox"><div class="page"><?php echo ($codeList["html"]); ?></div></div>
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
          </li>
          <?php if(is_array($inviteList["data"])): ?><?php $i = 0;?><?php $__LIST__ = $inviteList["data"]?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$vo): ?><?php ++$i;?><?php $mod = ($i % 2 )?><li>
            <div class="w0"><a href="<?php echo ($vo["space_url"]); ?>"><img src="<?php echo ($vo["avatar_small"]); ?>" width="30" height="30"/></a></div>
            <div class="w3"><a href="<?php echo ($vo["space_url"]); ?>"><?php echo ($vo["uname"]); ?></a></div>
            <div class="w1"><?php echo ($vo["email"]); ?></div>
          </li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
        </ul>
        <div class="page"><?php echo ($inviteList["html"]); ?></div>
        <?php endif; ?>
      </div>
      -->
    </div>
  </div>
</div>

<script type="text/javascript">
/**
 * 点击获取邀请码
 * @return void
 */
var getInviteCode = function()
{
	// 获取邀请码操作
	$.post(U('admin/Config/getInviteCode'), {}, function(res) {
		if(res.status == 1) {
			ui.success(res.info);
			setTimeout(function() {
				location.href = location.href;}, 1500);
			return false;
		} else {
			ui.error(res.info);
			return false;
		}
	}, 'json');
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