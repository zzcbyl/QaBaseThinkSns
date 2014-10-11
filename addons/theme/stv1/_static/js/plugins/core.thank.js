/**
 * 扩展核心评论对象
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */
core.thank = {
    // 给工厂调用的接口
    _init: function (attrs) {
        if (attrs.length == 4) {
            core.thank.init(attrs[1], attrs[2], attrs[3]);
        } else {
            return false;
        }
    },
    // 初始化评论对象
    init: function (obj, feed_id, uid) {
        // 参数验证
        if ('undefined' == typeof (obj) || 'undefined' == typeof (feed_id) || 'undefined' == typeof (uid)) {
            ui.error(L('PUBLIC_TIPES_ERROR'));
            return false;
        }
        // 感谢操作
        if ($(obj).attr('rel') == 'yes') {
            $.post(U('public/Index/thankAnswer'), { feedid: feed_id, uid: uid }, function (msg) {
                if (msg.status == 0) {
                    ui.error(msg.data);
                } else {
                    // 设置对象操作属性
                    $(obj).attr('rel', 'no');
                    $(obj).html("已感谢");
                    ui.success("感谢成功!");
                }
            }, 'json');
            return false;
        }
    }
}