
var shareContent = ''; 

function shareWeiXin(info, url) {

    $("#showShare").show();
    window.shareData.imgUrl = "http://www.luqinwenda.com/addons/theme/stv1/_static/image/weixinsharelogo.jpg";
    window.shareData.timeLineLink = url;
    window.shareData.sendFriendLink= url;
    window.shareData.tTitle = info;
    window.shareData.tContent = info;
    window.shareData.fTitle = info;
    window.shareData.fContent = info;
    shareContent = info;
}

document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
    window.shareData = {
        "imgUrl": "http://www.luqinwenda.com/addons/theme/stv1/_static/image/weixinsharelogo.jpg",
        "timeLineLink": "http://www.luqinwenda.com/index.php?app=public&mod=MobileNew&act=all",
        "sendFriendLink": "http://www.luqinwenda.com/index.php?app=public&mod=MobileNew&act=all",
        "tTitle": shareContent,
        "tContent": shareContent,
        "fTitle": shareContent,
        "fContent": shareContent,
    };

    // 发送给好友 
    WeixinJSBridge.on('menu:share:appmessage', function (argv) {
        WeixinJSBridge.invoke('sendAppMessage', {
            "img_url": window.shareData.imgUrl, // 
            "img_width": "640", // 
            "img_height": "640",
            "link": window.shareData.sendFriendLink,
            "desc": window.shareData.fContent,
            "title": window.shareData.fTitle
        }, function (res) {
            _report('send_msg', res.err_msg);
        });
    });

    // 分享到朋友圈 
    WeixinJSBridge.on('menu:share:timeline', function (argv) {
        WeixinJSBridge.invoke('shareTimeline', {
            "img_url": window.shareData.imgUrl,
            "img_width": "640",
            "img_height": "640",
            "link": window.shareData.timeLineLink,
            "desc": window.shareData.tContent,
            "title": window.shareData.tTitle
        }, function (res) {
            _report('timeline', res.err_msg);
        });
    });

}, false);


function viewProfile() {
    typeof WeixinJSBridge != "undefined" && WeixinJSBridge.invoke && WeixinJSBridge.invoke("profile", {
        username: 'gh_4309156ee263',
        scene: "57"
    });
}