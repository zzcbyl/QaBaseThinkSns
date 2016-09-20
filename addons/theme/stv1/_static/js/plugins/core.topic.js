// 提问核心Js操作
core.topic = {
    _init: function () {
        return true;
    },
    // 提问初始化
    init: function (agrs) {
        this.initNums = agrs.initNums; 	// 提问字数
        this.maxId = args.maxId, 		// 最大提问ID
		this.loadId = args.loadId, 		// 载入的提问ID
		this.feedType = args.feedType, 	// 提问类型
		this.loadmore = args.loadmore, 	// 是否载入更多
		this.uid = args.uid, 			// 当前提问列表对应的UID
		this.loadnew = args.loadnew; 	// 是否载入最新
        this.feed_type = args.feed_type;
        this.feed_key = args.feed_key;
        this.firstId = args.firstId;
        this.topic_id = args.topic_id; 	// 是否为话题
        this.gid = args.gid;
        this.inviteid = args.inviteid;
        //alert(agrs.feedid);
        this.answerid = agrs.answerid;

        //this.pre_page = "undefined" == typeof(pre_page) ? 1 :pre_page;//分页用到的前一页
        if ("undefined" == typeof (this.loadCount)) {
            this.loadCount = 1;
        }
        if (this.loadmore == 1) {
            this.canLoading = true; 	// 当前是否允许加载
            core.topic.bindScroll();
        } else {
            this.canLoading = false; // 当前是否允许加载
        }
        //this.startNewLoop();
        //		if($('#feed-lists').length > 0 && this.canLoading){
        //			$('#feed-lists').append("<div class='loading' id='loadMore'>" + L('PUBLIC_LOADING') + "<img src='" + THEME_URL + "/image/load.gif' class='load'></div>");
        //			core.topic.loadMoreFeed();
        //		}
    },
    // 页底加载提问
    bindScroll: function () {
        var _this = this;
        $(window).bind('scroll resize', function () {
            // 加载3次后，将不能自动加载提问
            if (_this.loadCount >= 4 || _this.canLoading == false) {
                return false;
            }
            var bodyTop = document.documentElement.scrollTop + document.body.scrollTop;
            var bodyHeight = $(document.body).height();
            if (bodyTop + $(window).height() >= bodyHeight - 250) {
                _this.loadCount = _this.loadCount + 1;
                if ($('#feed-lists').length > 0 && _this.loadId != null) {
                    $('#feed-lists').append("<div class='loading' id='loadMore'>" + L('PUBLIC_LOADING') + "<img src='" + THEME_URL + "/image/load.gif' class='load'></div>");
                    //alert('answerid='+this.answerid);
                    if (this.answerid != null && this.answerid != undefined) {
                        core.topic.loadMoreAnswer(this.answerid); //加载答案
                    }
                    else {
                        core.topic.loadMoreFeed();
                    }
                }
            }
        });
    },
    // 加载更多提问
    loadMoreFeed: function () {
        var _this = this;
        _this.canLoading = false;
        // 获取提问数据
        $.get(U('widget/PYQTopicList/loadMore'), { 'loadId': _this.loadId, 'type': _this.feedType, 'uid': _this.uid, 'feed_type': _this.feed_type, 'feed_key': _this.feed_key, 'fgid': fgid, 'topic_id': _this.topic_id, 'load_count': _this.loadCount, 'gid': _this.gid }, function (msg) {
            //alert(msg.loadId);
            // 加载失败
            if (msg.status == "0" || msg.status == "-1") {
                $('#loadMore').remove();
                if (msg.status == 0 && ("undefined" != typeof (msg.msg)) && _this.loadmore > 0) {
                    $('#feed-lists').append('<div class="loading" id="loadMore">' + msg.msg + '</div>');
                }
            }
            //alert(msg.html);
            // 加载成功

            if (msg.status == "1") {
                if (msg.firstId > 0 && _this.loadnew == 1) {
                    _this.firstId = msg.firstId;
                    // 启动查找最新的loop
                    //					_this.startNewLoop();
                }
                $('#loadMore').remove();
                if (_this.loadCount >= 4) {
                    var $lastDl = $('<div></div>');
                    $lastDl.html(msg.html);
                    //alert(msg.html);
                    msg.html = $lastDl.find('div').filter('.newTopicList').slice(30);
                    //alert(msg.html.html());
                }
                $('#feed-lists').append(msg.html);
                _this.canLoading = true;
                _this.loadId = msg.loadId;

                if (_this.loadCount >= 4) {
                    $('#feed-lists').append('<div id="page" class="page" style="display:none;">' + msg.pageHtml + '</div>');
                    if ($('#feed-lists .page').find('a').size() > 2) {
                        // 4ping + next 说明还有30个以上
                        var href = false;
                        $('#feed-lists .page').find('a').each(function () {
                            href = $(this).attr('href');
                        });
                        // 重组分页结构
                        $('#feed-lists .page').html(msg.pageHtml).show();
                        $('#feed-lists .page').find('a').each(function () {
                            var href = $(this).attr('href');
                            if (href) {
                                $(this).attr('href', 'javascript:;');
                                $(this).click(function () {
                                    core.topic.loadMoreByPage(href);
                                });
                            }
                        });
                    } else {
                        if ($('#feed-lists').find('dl').size() > 0) {
                            $('#feed-lists').append('<div class="loading" id="loadMore">' + L('PUBLIC_ISNULL') + '</div>');
                        }
                    }
                } else {
                    core.topic.bindScroll();
                }
                M(document.getElementById('feed-lists'));
            }
            zdylink();
        }, 'json')

        return false;
    },
    // 加载更多答案
    loadMoreAnswer: function (answerid) {
        var _this = this;
        _this.canLoading = false;
        // 获取提问数据
        $.get(U('widget/AnswerList/loadMore'), { 'feed_id': answerid, 'loadId': _this.loadId, 'type': _this.feedType, 'uid': _this.uid, 'feed_type': _this.feed_type, 'feed_key': _this.feed_key, 'fgid': fgid, 'topic_id': _this.topic_id, 'load_count': _this.loadCount, 'gid': _this.gid }, function (msg) {
            // 加载失败
            if (msg.status == "0" || msg.status == "-1") {
                $('#loadMore').remove();
                if (msg.status == 0 && ("undefined" != typeof (msg.msg)) && _this.loadmore > 0) {
                    $('#feed-lists').append('<div class="loading" id="loadMore">' + msg.msg + '</div>');
                }
            }
            // 加载成功
            if (msg.status == "1") {
                if (msg.firstId > 0 && _this.loadnew == 1) {
                    _this.firstId = msg.firstId;
                    // 启动查找最新的loop
                    //					_this.startNewLoop();
                }
                $('#loadMore').remove();
                if (_this.loadCount >= 4) {
                    var $lastDl = $('<div></div>');
                    $lastDl.html(msg.html);
                    msg.html = $lastDl.find('dl').filter('.feed_list').slice(30);
                }
                $('#feed-lists').append(msg.html);
                _this.canLoading = true;
                _this.loadId = msg.loadId;
                if (_this.loadCount >= 4) {
                    $('#feed-lists').append('<div id="page" class="page" style="display:none;">' + msg.pageHtml + '</div>');
                    if ($('#feed-lists .page').find('a').size() > 2) {
                        // 4ping + next 说明还有30个以上
                        var href = false;
                        $('#feed-lists .page').find('a').each(function () {
                            href = $(this).attr('href');
                        });
                        // 重组分页结构
                        $('#feed-lists .page').html(msg.pageHtml).show();
                        $('#feed-lists .page').find('a').each(function () {
                            var href = $(this).attr('href');
                            if (href) {
                                $(this).attr('href', 'javascript:;');
                                $(this).click(function () {
                                    core.topic.loadMoreByPage(href);
                                });
                            }
                        });
                    } else {
                        if ($('#feed-lists').find('dl').size() > 0) {
                            $('#feed-lists').append('<div class="loading" id="loadMore">' + L('PUBLIC_ISNULL') + '</div>');
                        }
                    }
                } else {
                    core.topic.bindScroll();
                }
                M(document.getElementById('feed-lists'));
            }
        }, 'json')
        return false;
    },
    // 分页加载更多数据
    loadMoreByPage: function (href) {
        var obj = this;
        obj.canLoading = false;
        $('#feed-lists').html("<div class='loading' id='loadMore'>" + L('PUBLIC_LOADING') + "<img src='" + THEME_URL + "/image/load.gif' class='load'></div>");
        scrolltotop.scrollup();
        //alert(href);
        $.get(href, {}, function (msg) {
            if (msg.status == "0" || msg.status == "-1") {
                $('#feed-lists').append("<div class='load' id='loadMore'>'+L('PUBLIC_ISNULL')+'</div>");
            } else {
                $('#feed-lists').html(msg.html);
                $('#feed-lists').append('<div id="page" class="page" >' + msg.pageHtml + '</div>');

                $('#feed-lists .page').find('a').each(function () {
                    var href = $(this).attr('href');
                    if (href) {
                        $(this).attr('href', 'javascript:void(0);');
                        $(this).click(function () {
                            core.topic.loadMoreByPage(href);
                        });
                    }
                });
                //core.topic.bindScroll();
                M(document.getElementById('feed-lists'));
            }
        }, 'json');
        return false;
    },
    // 加载最新提问
    startNewLoop: function () {
        var _this = this;
        var searchNew = function () {
            if (_this.firstId < 1) {
                return false;
            }
            // 加载最新的数据
            $.post(U('widget/FeedList/loadNew'), { maxId: _this.firstId, type: 'new' + _this.feedType, uid: _this.uid }, function (msg) {
                if (msg.status == 1 && msg.count > 0) {
                    _this.showNew(msg.count);
                    _this.tempHtml = msg.html;
                    _this.tmpfirstId = msg.maxId;
                }
            }, 'json');
        };
        // 每2分钟查找一次最新提问
        var loop = setInterval(searchNew, 120000);
    },
    // 提示有多少新提问数据
    showNew: function (nums) {
        if ($('#feed-lists').find('.notes').length > 0) {
            $('#feed-lists').find('.notes').html(L('PUBLIC_WEIBO_NUM', { 'sum': nums }));
        } else {
            var html = '<a href="javascript:core.topic.showNewList()" class="notes">' + L('PUBLIC_WEIBO_NUM', { 'sum': nums }) + '</a>';
            $('#feed-lists').prepend(html);
        }
    },
    showNewList: function () {
        $('#feed-lists').find('.notes').remove();
        $('#feed-lists').prepend(this.tempHtml);
        this.firstId = this.tmpfirstId;
        this.tempHtml = '';
        M(document.getElementById('feed-lists'));
    },
    // 发布提问之后操作
    afterPost: function (obj, textarea, topicHtml, description_editor, description, close, questionid, int_isadd) {
        if (topicHtml == '') {
            textarea.value = '';
            if (description != undefined)
                description.value = ''; ;
        } else {
            textarea.value = topicHtml;
        }

        //obj.parentModel.parentModel.childModels['numsLeft'][0].innerHTML = L('PUBLIC_INPUT_TIPES', { 'sum': '<span>' + initNums + '</span>' });
        var fadeOutObj = function () {
            textarea.ready = null;
            if (description != undefined)
                description.ready = null;
        };
        $(obj.childModels['post_ok'][0]).fadeOut(3000, fadeOutObj);
        //追问或者要求补充
        if (int_isadd == 1) {
            //$('#AddQuestionDiv').hide();
            //$('#SupplementAnswer_' + questionid).hide();
            return;
        }
        //提问增加提问数
        if (parseInt($('#twCount a').html()) >= 0) {
            $('#twCount a').html(parseInt($('#twCount a').html()) + 1);
        }
        //回答完成隐藏回答框
        if (questionid > 0) {
            if (parseInt($('#hdCount a').html()) >= 0) {
                $('#hdCount a').html(parseInt($('#hdCount a').html()) + 1);
            }
            $('#AnswerQuestionDiv').remove();
        }
        // 修改提问数目
        if ("undefined" == typeof (close) || !close) {
            updateUserData('weibo_count', 1);
        }
        if ("undefined" != typeof (core.uploadFile)) {
            core.uploadFile.removeParentDiv();
        }
        if ("undefined" != typeof (core.contribute)) {
            //core.contribute.resetBtn();
        }
    },
    // 将json数据插入到feed-lists中
    insertToList: function (html, feedId, questionid, int_isadd) {
        //alert(html);exit;
        if ("undefined" == typeof (html) || html == '') {
            return false;
        }
        //alert(123);exit;
        if ($('#feed-lists').length > 0) {
            if (questionid == undefined || questionid <= 0) { //提交问题
                var before = $('#feed-lists div').eq(0);

                $div = $("<div style=\"border:1px solid #ccc; margin-bottom:10px;\"><div style=\"position:absolute; \"><img src=\"" + THEME_URL + "/image/question_small.jpg\"  /></div><dl id=\"feed" + feedId + "\" model-node=\"feed_list\" class=\"feed_list\" style=\"border:0; padding:15px 5px 10px 40px;\">" + html + "</dl></div>");

                if (before.length > 0) {
                    $div.insertBefore(before);
                } else {
                    if ($('#feed-lists').find('div').size() > 0) {
                        $('#feed-lists').append($div);
                    } else {
                        $('#feed-lists').html($div);
                    }
                }
            }
            else {
                //追问或者要求补充
                if (int_isadd == 1) {
                    return;
                }
                //提交回答
                var before = $('#feed-lists div').eq(0);
                $div = $('<div>' + html + '</div>');
                var _this = this;
                var answerPTime = $($div.find("#answer_publish_time")[0]).val();
                if (answerPTime != '') {
                    _this.loadId = answerPTime;
                }
                if (before.length > 0) {
                    $div.insertBefore(before);
                } else {
                    if ($('#feed-lists').find('div').size() > 0) {
                        $('#feed-lists').append($div);
                    } else {
                        $('#feed-lists').html($div);
                    }
                }
            }
            //M($dl[0]);
        }
        //DIY专用
        if ($('#feed-lists-d').length > 0) {
            var before = $('#feed-lists-d dl').eq(0);
            var _dl = document.createElement('dl');
            _dl.setAttribute('class', 'feed_list');
            _dl.setAttribute('model-node', 'feed_list');
            _dl.setAttribute('id', 'feed' + feedId);
            _dl.innerHTML = html;
            if (before.length > 0) {
                $(_dl).insertBefore(before);
            } else {
                if ($('#feed-lists-d').find('dl').size() > 0) {
                    $('#feed-lists-d').append(_dl);
                } else {
                    $('#feed-lists-d').html(_dl);
                }
            }
            M(_dl);
        }
        if ("undefined" != typeof (after_publish_weibo)) {
            after_publish_weibo(feedId);
        }
    },
    // 检验提问内容，obj = 要验证的表单对象，post = 表示是否发布
    checkNums: function (obj, post) {
        //        if ("undefined" == typeof (obj.parentModel.parentModel.parentModel.childModels['numsLeft'])) {
        //            return true;
        //        }
        //alert(obj.value);
        var titleValue = obj.value;
        if (obj.value == "请用一句话描述您的困惑")
            titleValue = "";

        // 获取输入框中还能输入的数字个数
        var strlen = core.getLength(titleValue, true);
        // 匹配尾部空白符
        if ($.trim(titleValue) !== '') {
            var blank = titleValue.match(/\s+$/g);
            if (blank !== null) {
                strlen += Math.ceil(blank[0].length / 2);
            }
        }
        if (obj.parentModel.parentModel.parentModel.childModels['numsLeft'] == undefined) {
            var leftNums = initNums - strlen;
            if (leftNums == initNums && 'undefined' != typeof (post)) {
                return false;
            }
            // 获取按钮对象
            var objInput = '';
            if ($(obj.parentModel.parentModel.childModels['send_action']).html() != null) {
                objInput = $(obj.parentModel.parentModel.childModels['send_action'][0]).find('a').eq(0);
            }
            // 获取剩余字数
            if (leftNums >= 0) {
                //var html = (leftNums == initNums) ? L('PUBLIC_INPUT_TIPES', { 'sum': '<span>' + leftNums + '</span>' }) : L('PUBLIC_PLEASE_INPUT_TIPES', { 'sum': '<span>' + leftNums + '</span>' });
                var html = leftNums;
                //alert(html);
                $("#numsLeft").html(html);
                //obj.parentModel.parentModel.parentModel.childModels['numsLeft'][0].innerHTML = html;
                $(obj).removeClass('fb');
                if (leftNums == initNums && $(obj).find('img').size() == 0) {
                    if (typeof (objInput) == 'object') {
                        //objInput[0].className = 'btn-grey-white';
                    }
                    return false; // 没有输入内容
                }
                if (typeof (objInput) == 'object') {
                    //objInput[0].className = 'btn-green-big';
                }
                return true;
            } else {
                //var html = L('PUBLIC_INPUT_ERROR_TIPES', { 'sum': '<span style="color:red">' + Math.abs(leftNums) + '</span>' });
                //alert(html);
                var html = leftNums;
                $(obj).addClass('fb');
                $("#numsLeft").html(html);
                //obj.parentModel.parentModel.parentModel.childModels['numsLeft'][0].innerHTML = html;
                if (typeof (objInput) == 'object') {
                    //objInput[0].className = 'btn-grey-white';
                }
                return false;
            }
        }
        else {
            var leftNums = shareNums - strlen;
            if (leftNums == shareNums && 'undefined' != typeof (post)) {
                return false;
            }
            // 获取按钮对象
            var objInput = '';
            if ($(obj.parentModel.parentModel.childModels['send_action']).html() != null) {
                objInput = $(obj.parentModel.parentModel.childModels['send_action'][0]).find('a').eq(0);
            }
            // 获取剩余字数
            if (leftNums >= 0) {
                var html = (leftNums == initNums) ? L('PUBLIC_INPUT_TIPES', { 'sum': '<span>' + leftNums + '</span>' }) : L('PUBLIC_PLEASE_INPUT_TIPES', { 'sum': '<span>' + leftNums + '</span>' });
                obj.parentModel.parentModel.parentModel.childModels['numsLeft'][0].innerHTML = html;
                $(obj).removeClass('fb');
                if (leftNums == initNums && $(obj).find('img').size() == 0) {
                    if (typeof (objInput) == 'object') {
                        //objInput[0].className = 'btn-grey-white';
                    }
                    return false; // 没有输入内容
                }
                if (typeof (objInput) == 'object') {
                    //objInput[0].className = 'btn-green-big';
                }
                return true;
            } else {
                var html = L('PUBLIC_INPUT_ERROR_TIPES', { 'sum': '<span style="color:red">' + Math.abs(leftNums) + '</span>' });
                $(obj).addClass('fb');
                obj.parentModel.parentModel.parentModel.childModels['numsLeft'][0].innerHTML = html;
                if (typeof (objInput) == 'object') {
                    //objInput[0].className = 'btn-grey-white';
                }
                return false;
            }
        }
    },
    //邀请回答某个问题
    Invite: function (feedID, inviteList) {
        var url = U('public/profile/invitefriendanswer');
        if (inviteList.indexOf(inviteList.length - 1, 1) == ',')
            inviteList = inviteList.substring(0, inviteList.length - 1);
        //alert(url);
        //alert(inviteList);
        $.post(url, { QuestionID: feedID, InviteUids: inviteList }, function (msg) {
            ui.showMessage(msg.data, 2, 3);
            $("#InvitedFriend").html('');
        }, 'json');

    },
    // 发布提问
    post_feed: function (_this, mini_editor, textarea, description_editor, description, questionid, isbox, url, isAdd, inviteList) {
        var obj = this;
        // 避免重复发送
        if ("undefined" == typeof (obj.isposting)) {
            obj.isposting = true;
        } else {
            if (obj.isposting == true) {
                return false;
            }
        }

        if ("undefined" == typeof (isbox)) {
            isbox = false;
        }

        //分享到新浪微博
        var sinaShare = 0;
        if ($("#cbx_sina").attr("checked") == 'checked') {
            sinaShare = 1;
        }

        //分享到腾讯微博
        var qqShare = 0;
        if ($("#cbx_qq").attr("checked") == 'checked') {
            qqShare = 1;
        }

        // 提问类型在此区分
        var args = $(_this).attr('event-args');
        var setargs = args.replace('type=postvideo', 'type=post');

        var attrs = M.getEventArgs(_this);
        var attachobj = $(_this.parentModel).find('.attach_ids');
        if (attachobj.length > 0) {
            var type = (attachobj.attr('feedtype') == 'image') ? 'postimage' : 'postfile';
            var attach_id = attachobj.val();
        } else {
            var attach_id = '';
            var type = attrs.type;
        }
        //邀请回答的表主键ID
        var inviteid = 0;
        if (typeof (attrs.inviteid) != 'undefined')
            inviteid = attrs.inviteid;

        var videourl = $('#postvideourl').val();
        var app_name = attrs.app_name;

        if (questionid == undefined && obj.checkNums(textarea, 'post') == false) {
            //alert('34444');
            if (type == 'postimage') {
                textarea.value = L('PUBLIC_SHARE_IMAGES');
            } else if (type == 'postfile') {
                textarea.value = L('PUBLIC_SHARE_FILES');
            } else {
                flashTextarea(textarea);
                obj.isposting = false;
                return false;
            }
        }

        // 获取投稿ID
        var channel_id = $.trim($('#contribute').val());
        // 为空处理
        var data = textarea.value;
        if (data == '' || data.length < 0) {
            // TODO 只有一次情况才会执行到这里面 一般是不会的
            ui.error(L('PUBLIC_CENTE_ISNULL'));
            obj.isposting = false;
            return false;
        }
        data = removeHTMLTag(data);
        if (data == '' || data.length < 0) {
            ui.error('请勿输入非法与敏感字符');
            obj.isposting = false;
            return false;
        }

        var txtVal = '';
        if (description != undefined) {
            txtVal = description.value;
            if (txtVal == "您可以在这里继续补充问题细节")
                txtVal = "";

            if (txtVal != "") {
                txtVal = removeHTMLTag(txtVal);
                if (txtVal == '' || txtVal.length < 0) {
                    ui.error('请勿输入非法与敏感字符');
                    obj.isposting = false;
                    return false;
                }
            }
        }

        txtVal = txtVal.replace(/\n/g, "<br />")

        if (!url || url == "") {
            url = U('public/Feed/PostFeed');
        }

        var Qid = 0;
        if (questionid != null && questionid != undefined)
            Qid = questionid.value;
        var int_isadd = 0;
        if (isAdd)
            int_isadd = 1;

        // 发布提问
        $.post(url, { body: data, type: type, app_name: app_name, content: '', attach_id: attach_id, videourl: videourl, channel_id: channel_id, source_url: attrs.source_url, gid: attrs.gid, description: txtVal, questionid: Qid, addask: int_isadd, inviteid: inviteid, ShareSina: sinaShare, ShareQQ: qqShare }, function (msg) {
            //alert(msg.data);
            //return;
            obj.isposting = false;
            //_this.className = 'btn-grey-white';
            //$(_this).html('<span>' + L('PUBLIC_SHARE_BUTTON') + '</span>');
            if (msg.status == 1) {
                if ("undefined" != typeof (core.uploadFile)) {
                    core.uploadFile.clean();
                }
                var postOk = mini_editor.childModels['post_ok'][0];
                $(postOk).fadeIn('fast');
                core.topic.afterPost(mini_editor, textarea, attrs.topicHtml, description_editor, description, false, Qid, int_isadd);
                if (!isbox) {
                    core.topic.insertToList(msg.data, msg.feedId, questionid, int_isadd);
                } else {
                    ui.box.close();
                    var mini = M.getModelArgs(mini_editor);
                    ui.success(mini.prompt);
                    if (document.getElementById('feed-lists') != null && channel_id == 0) {
                        setTimeout(function () {
                            core.topic.insertToList(msg.data, msg.feedId, questionid);
                        }, 1500);
                    }
                }

                //邀请回答
                if ("undefined" != typeof (inviteList) && inviteList.value != "") {
                    core.topic.Invite(msg.feedId, inviteList.value);
                }

                M.setArgs(_this, setargs);
            } else {
                ui.error(msg.data);
            }
        }, 'json');
        return false;
    },
    friendlyDate: function (sTime, cTime) {
        var formatTime = function (num) {
            return (num < 10) ? '0' + num : num;
        };

        if (!sTime) {
            return '';
        }

        var cDate = new Date(cTime * 1000);
        var sDate = new Date(sTime * 1000);
        var dTime = cTime - sTime;
        var dDay = parseInt(cDate.getDate()) - parseInt(sDate.getDate());
        var dMonth = parseInt(cDate.getMonth() + 1) - parseInt(sDate.getMonth() + 1);
        var dYear = parseInt(cDate.getFullYear()) - parseInt(sDate.getFullYear());

        if (dTime < 60) {
            if (dTime < 10) {
                return '刚刚';
            } else {
                return parseInt(Math.floor(dTime / 10) * 10) + '秒前';
            }
        } else if (dTime < 3600) {
            return parseInt(Math.floor(dTime / 60)) + '分钟前';
        } else if (dYear === 0 && dMonth === 0 && dDay === 0) {
            return '今天' + formatTime(sDate.getHours()) + ':' + formatTime(sDate.getMinutes());
        } else if (dYear === 0) {
            return formatTime(sDate.getMonth() + 1) + '月' + formatTime(sDate.getDate()) + '日 ' + formatTime(sDate.getHours()) + ':' + formatTime(sDate.getMinutes());
        } else {
            return sDate.getFullYear() + '-' + formatTime(sDate.getMonth() + 1) + '-' + formatTime(sDate.getDate()) + ' ' + formatTime(sDate.getHours()) + ':' + formatTime(sDate.getMinutes());
        }
    }
};