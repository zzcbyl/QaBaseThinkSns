<?php if (!defined('THINK_PATH')) exit();?><!-- 关注成功后，设置分组弹窗 -->
<div class="layer-follow pop">
    <div class="tit">
    	<?php echo L('PUBLIC_PEOPLE_REMARK');?>： 
    	<input name="" type="text" class="s-txt"  onblur="if(this.value == '')this.value='<?php echo L('PUBLIC_REMARK_SETTING');?>';" onfocus="if(this.value == '<?php echo L('PUBLIC_REMARK_SETTING');?>')this.value='';" value="<?php echo L('PUBLIC_REMARK_SETTING');?>" id="remark" />
    </div>

	<div class="tit"><span><?php echo L('PUBLIC_PEOPLE_GROUP_SETTING',array('link'=>$fuserInfo['space_link']));?></span></div>
    <div class="group-name">
        <ul id="followGroupSelector">
            <?php if(is_array($group_list)): ?><?php $i = 0;?><?php $__LIST__ = $group_list?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$g): ?><?php ++$i;?><?php $mod = ($i % 2 )?><li>
                <a href="javascript:void(0)" class="right hover" onclick="editGroup(<?php echo ($g["follow_group_id"]); ?>)"><?php echo L('PUBLIC_EDIT_GROUP');?></a>
                <label ><input type="checkbox" class="s-ck" name="gids[]" value="<?php echo ($g["follow_group_id"]); ?>" onclick="setFollowGroup(this.value)" /> 
                <span id='title_<?php echo ($g["follow_group_id"]); ?>'><?php echo ($g["title"]); ?></span></label>
                <span id='edit_<?php echo ($g["follow_group_id"]); ?>' style="display:none">
                <input class="s-txt" type="text" value='<?php echo ($g["title"]); ?>' onblur="this.className='s-txt'" onfocus="this.className='s-txt-focus'" >
                <a href="javascript:;" onclick="saveGroup(<?php echo ($g["follow_group_id"]); ?>);" class="btn-green-small"><span><?php echo L('PUBLIC_SAVE');?></span></a>
                <a href="javascript:;" onclick="cenSaveGroup(<?php echo ($g["follow_group_id"]); ?>);" class="btn-cancel"><span><?php echo L('PUBLIC_CANCEL');?></span></a>
                </span>
            </li><?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
        </ul>
    </div>

    <?php if(count($group_list) < 10): ?>
    <div>
        <div class="new-add" id="openTab"><a href="javascript:void(0);" onclick="createFollowGroupTab('')" class="openTab">+<?php echo L('PUBLIC_CREATE_GROUP');?></a></div>
        <dl>
            <dd id="createFollowGroup" class="mt10">
                <input type="text" name="followGroupTitle" value="" class="s-txt" style="display:none;" onblur="this.className='s-txt'" onfocus="this.className='s-txt-focus'" >
                <a onclick="createFollowGroup()" class="btn-green-small mr5" style="display:none;"><span><?php echo L('PUBLIC_CREATE');?></span></a>
                <a href="javascript:void(0);" onclick="createFollowGroupTab('close')" class="btn-cancel" style="display:none;"><span><?php echo L('PUBLIC_CANCEL');?></span></a>
            </dd>
        </dl>
    </div>
    <?php endif; ?>

    <div class="right mt10" style="_height:32px">
    	<a onclick="saveRemark();" class="btn-green-big"><span><?php echo L('PUBLIC_SAVE');?></span></a>
    	<a onclick="ui.box.close();" class="btn-grey-white ml10"><span><?php echo L('PUBLIC_CANCEL');?></span></a>
    </div>
</div>

<script type="text/javascript">
$(function() {
	setTimeout(function() {
		$('#remark').focus();
	}, 300);
});
/**
 * 显示编辑弹窗
 * @param integer gid 分组ID
 * @return void
 */
var editGroup = function(gid) {
	$('#title_'+gid).hide();
	$('#edit_'+gid).show();
};
/**
 * 保存分组操作
 * @param integer gid 分组ID
 * @return void
 */
var saveGroup = function(gid) {
	var title = $('#edit_'+gid).find('input').val();
	if(title == '') {
		ui.error('<?php echo L('PUBLIC_GROUPNAME_INPUT');?>');
		return false;
	}
	if(title != $('#title_'+gid).html()) {
		if(getLength(title) > 10 ){
			ui.error('<?php echo L('PUBLIC_GROUP_CHARACTER_LIMIT');?>');
			return false;
		}
		$.post(U('public/FollowGroup/saveGroup'),{gid:gid,title:title},function(msg){
			if(msg.status == 0){
				ui.error(msg.info);
				$('#edit_'+gid).find('input').val($('#title_'+gid).html());
			} else {
				$('#title_'+gid).html(title);
			}
		},'json');
	}
	cenSaveGroup(gid);
};
/**
 * 隐藏编辑弹窗
 * @param integer gid 分组ID
 * @return void
 */
var cenSaveGroup = function(gid) {
	$('#title_'+gid).show();
	$('#edit_'+gid).hide();
};
// 用户ID
var fid = '<?php echo ($fid); ?>';
$(document).ready(function(){
	<?php if(is_array($f_group_status)): ?><?php $i = 0;?><?php $__LIST__ = $f_group_status?><?php if( count($__LIST__)==0 ) : echo "" ; ?><?php else: ?><?php foreach($__LIST__ as $key=>$gs): ?><?php ++$i;?><?php $mod = ($i % 2 )?>$( "input[name='gids[]'][value='<?php echo ($gs["gid"]); ?>']" ).attr( 'checked',true );<?php endforeach; ?><?php endif; ?><?php else: echo "" ;?><?php endif; ?>
});
/**
 * 设置分组操作
 * @param integer gid 分组ID
 * @return void
 */
var setFollowGroup = function(gid) {
	return false;
	$.post(U('public/FollowGroup/setFollowGroup'),{gid:gid,fid:fid},function(res){
		$('.followGroup'+fid).html(res.title);
	}, 'json');
};
/**
 * 设置分组操作 - 多分组
 * @return void
 */
var setFollowGroups = function() {
	// 获取选中的分组
	var checked = [];
	$('input[name="gids[]"]').each(function(i, n) {
		if($(this).attr('checked')) {
			checked.push(parseInt($(this).val()));
		}
	});
	// if(checked.length == 0) {
	// 	return false;
	// }
	var gids = checked.join(',');
	// 设置分组操作
	$.post(U('public/FollowGroup/setFollowGroups'), {gids:gids, fid:fid}, function() {
	}, 'json');
};
/**
 * 创建关注分组Tab显示
 * @param string action 操作类型
 * @return void
 */
var createFollowGroupTab = function(action) {
	if(action == 'close'){
		$("input[name='followGroupTitle']").val(' ');
		$('#createFollowGroup input').css('display','none');
		$('#createFollowGroup .btn-green-small').css('display','none');
		$('#createFollowGroup .btn-cancel').css('display','none');
		$('#createFollowGroup .openTab').css('display','inline-block');
        // 隐藏弹窗
        var len = $('#followGroupSelector').find("li").length;
        if(len > 9) {
            $('#openTab').css('display','none');
        } else {
            $('#openTab').css('display','inline-block');
        }
	}else{
		$('#createFollowGroup input').css('display','inline');
		$('#createFollowGroup .btn-green-small').css('display','inline-block');		
		$('#createFollowGroup .btn-cancel').css('display','inline-block');
		$('#openTab').css('display','none');
	}
};
/**
 * 创建关注分组操作
 * @return void
 */
var createFollowGroup = function() {
	var title = $("input[name='followGroupTitle']").val();
	if(title == ''){
		ui.error('<?php echo L("PUBLIC_GROUPNAME_INPUT");?>');
		return false;
	}
	if(getLength(title) > 10 ){
		ui.error('<?php echo L('PUBLIC_GROUP_CHARACTER_LIMIT');?>');
		return false;
	}
	$.post(U('public/FollowGroup/setGroup'),{title:title},function(gid){
	    var res = eval('('+gid+')');
        if(res.status != 0){
            
            var html = '<li>\
			            <a onclick="editGroup(' + res.info + ')" class="right hover" href="javascript:void(0)"><?php echo L('PUBLIC_EDIT_GROUP');?></a>\
			            <label><input type="checkbox" onclick="setFollowGroup(this.value)" value="' + res.info + '" name="gids[]" class="s-ck">\
			            <span id="title_' + res.info + '">' + $.trim(title) + '</span></label>\
			            <span style="display:none" id="edit_' + res.info + '">\
			            <input type="text" onfocus="this.className=\'s-txt-focus\'" onblur="this.className=\'s-txt\'" value="' + $.trim(title) + '">\
			            <a class="btn-green-small" onclick="saveGroup(' + res.info + ');" href="javascript:;"><span><?php echo L('PUBLIC_SAVE');?></span></a>\
			            <a class="btn-cancel" onclick="cenSaveGroup(' + res.info + ');" href="javascript:;"><span><?php echo L('PUBLIC_CANCEL');?></span></a>\
			            </span></li>';
			$('#followGroupSelector').append(html);
			createFollowGroupTab('close');
            setFollowGroup(res.info);
        }else{
            ui.error(res.info);
        }
	});
};
/**
 * 保存备注信息
 * @return void
 */
var saveRemark = function() {
	// 备注操作
	var remark = $('#remark').val();
	if(getLength(remark) > 10) {
		ui.error('<?php echo L('PUBLIC_REMARK_CHARACTER_LIMIT');?>');
		return false;
	}
	// 设置关注分组
	setFollowGroups();
	if($('#remark').val() =='' || $('#remark').val() =='<?php echo L('PUBLIC_REMARK_SETTING');?>'){
		ui.success('<?php echo L('PUBLIC_SAVE_SUCCESS');?>');
		setTimeout(function (){
			ui.box.close();
		},2000)
	} else {
		$.post(U('public/FollowGroup/saveRemark'),{remark:remark,fid:fid},function(msg){
			if(msg.status == 0){
				ui.box.close();
				ui.error(msg.data);
			}else{
				if(remark==''){
					$('.remark_'+fid).html('<?php echo L('PUBLIC_REMARK_SETTING');?>');
					$('.remark_'+fid).attr('remark','');
				}else{
					if($('.remark_'+fid)){
						$('.remark_'+fid).html(remark);
						$('.remark_'+fid).attr('remark',encodeURIComponent(remark));
					}
				}
				if("undefined" != typeof(core.facecard) ){
						core.facecard.deleteUser(fid);
				}
				ui.success('<?php echo L('PUBLIC_SAVE_SUCCESS');?>!');
				setTimeout(function (){
					ui.box.close();
				},2000)
			}
		}, 'json');
	}
};
</script>