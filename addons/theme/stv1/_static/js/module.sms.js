
var sendSms = {
    node: null,
    count: 60,
    start: function () {
        if (this.count > 0) {
            this.node.value = "再次获取（" + this.count-- + "）";
            var _this = this;
            setTimeout(function () {
                _this.start();
            }, 1000);
        } else {
            this.node.removeAttribute("disabled");
            this.node.value = "再次获取";
            this.count = 60;
        }
    },
    //初始化
    init: function (node) {
        this.node = node;
        this.node.setAttribute("disabled", true);
        this.start();
    }
};

var btn = document.getElementById("btnGetCode");
btn.onclick = function () {
    var url = $('#sendUrl').val();
    var number = $("#account").val().Trim();
    if (number != "") {
        $.post(url, { Number: number }, function (msg) {
            if (msg.status == 0) {
                ui.error(msg.data);
            } else {
                ui.success(msg.data);
                $("#SmsCode").val(msg.code);
                sendSms.init(btn);
            }
        }, 'json');
    }
    else {
        ui.error("请输入手机号");
    }
};
    