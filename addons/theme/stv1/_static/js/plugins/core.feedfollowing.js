/**
* 关注问题模型Js核心插件
* @author zhangzc
* @version TS3.0
*/
core.feedfollowing = {
    // 初始化参数
    _init: function (attrs) {
        // 转化为数组
        if (attrs.length >= 4) {
            core.feedfollowing.init(attrs[1], attrs[2], attrs[3], attrs[4]);
        } else {
            return false;
        }
    },
    init: function (obj, type, fid, isIco) {
        // 参数验证
        if ('undefined' == typeof (obj) || 'undefined' == typeof (fid)) {
            ui.error(L('PUBLIC_TIPES_ERROR'));
            return false;
        }
        // 添加关注操作
        if ($(obj).attr('rel') == 'add') {
            $.post(U('widget/FeedFollowing/addFeedFollowing'), { fid: fid }, function (msg) {
                if (msg.status == 0) {
                    ui.error(msg.data);
                } else {
                    // 设置对象操作属性
                    $(obj).attr('rel', 'remove');

                    if ($('.count_' + fid).length > 0) {
                        if (isIco == 1) {
                            $(obj).find('i').eq(0).addClass('current');
                        } else {
                            $(obj).html('已关注');
                        }
                        var nums = $('.count_' + fid).html();
                        $('.count_' + fid).html(parseInt(nums) + 1);
                    } else {
                        $(obj).html('取消关注');
                    }
                    //updateUserData('favorite_count', 1);
                    ui.success('关注成功');
                }
            }, 'json');
            return false;
        }
        // 删除关注操作
        if ($(obj).attr('rel') == 'remove') {
            $.post(U('widget/FeedFollowing/delFeedFollowing'), { fid: fid }, function (msg) {
                if (msg.status == 1) {
                    //updateUserData('favorite_count', -1);
                    if (type != 'collection') {
                        $(obj).attr('rel', 'add');
                        if (isIco == 1) {
                            $(obj).find('i').eq(0).removeClass('current');
                        } else {
                            $(obj).html('关注');
                        }
                        if ($('.count_' + fid).length > 0) {
                            var nums = $('.count_' + fid).html();
                            $('.count_' + fid).html(parseInt(nums) - 1);
                        }
                    } else {
                        $('#feed' + fid).fadeOut('slow');
                    }
                } else {
                    ui.error(msg.data);
                }
            }, 'json');
            return false;
        }
    }
};