<?php if (!defined('THINK_PATH')) exit();?><span class='remark' >
<?php if(!empty($remark)){ ?>
<em>(</em><a href="javascript:;" title='<?php echo L('PUBLIC_CLICK_EDIT');?>' event-node='setremark' remark='<?php echo (urlencode($remark)); ?>' class ="remark_<?php echo ($uid); ?>" uid='<?php echo ($uid); ?>'><?php echo ($remark); ?></a><em>)</em>
<?php }else{ ?>
<?php if(($showonly)  !=  "1"): ?><em>(</em><a href="javascript:;" title='<?php echo L('PUBLIC_CLICK_SETING');?>' event-node='setremark' remark='' class ="remark_<?php echo ($uid); ?>" uid='<?php echo ($uid); ?>'><?php echo L('PUBLIC_REMARK_SETTING');?></a><em>)</em><?php endif; ?>
<?php } ?>
</span>