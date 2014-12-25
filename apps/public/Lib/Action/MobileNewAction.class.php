<?php

/**
 * SearchAction 搜索模块
 * @version TS3.0
 */
class MobileNewAction
{
    /**
     * 架构函数 取得模板对
     * @access public
     */
    public function __construct()
    {
        $this->initSite();

    }

    /**
     * 站点信息初始化
     * @access private
     * @return void
     */
    private function initSite()
    {

        //载入站点配置全局变量
        $this->site = model('Xdata')->get('admin_Config:site');

        if ($this->site['site_closed'] == 0 && APP_NAME != 'admin') {
            //TODO  跳转到站点关闭页面
            $this->page404($this->site['site_closed_reason']);
            exit();
        }

        //检查是否启用rewrite
        if (isset($this->site['site_rewrite_on'])) {
            C('URL_ROUTER_ON', ($this->site['site_rewrite_on'] == 1));
        }

        //初始化语言包
        $cacheFile = C('F_CACHE_PATH') . '/initSiteLang.lock.php';
        if (!file_exists($cacheFile)) {
            model('Lang')->initSiteLang();
        }

        //LOGO处理
        $this->site['logo'] = getSiteLogo($this->site['site_logo']);

        //默认登录后首页
        if (intval($this->site['home_page'])) {
            $appInfo = model('App')->where('app_id=' . intval($this->site['home_page']))->find();
            $this->site['home_url'] = U($appInfo['app_name'] . '/' . $appInfo['app_entry']);
        } else {
            $this->site['home_url'] = U('public/Index/index');
        }

        //赋值给全局变量
        $GLOBALS['ts']['site'] = $this->site;

        //网站导航
        $GLOBALS['ts']['site_top_nav'] = model('Navi')->getTopNav();
        $GLOBALS['ts']['site_bottom_nav'] = model('Navi')->getBottomNav();
        $GLOBALS['ts']['site_bottom_child_nav'] = model('Navi')->getBottomChildNav($GLOBALS['ts']['site_bottom_nav']);

        //获取可搜索的内容列表
        if (false === ($searchSelect = S('SearchSelect'))) {
            $searchSelect = D('SearchSelect')->findAll();
            S('SearchSelect', $searchSelect);
        }

        //网站所有的应用
        $GLOBALS['ts']['site_nav_apps'] = model('App')->getAppList(array('status' => 1, 'add_front_top' => 1), 9);

        //网站全局变量过滤插件
        Addons::hook('core_filter_init_site');

        $this->assign('site', $this->site);
        $this->assign('site_top_nav', $GLOBALS['ts']['site_top_nav']);
        $this->assign('site_bottom_nav', $GLOBALS['ts']['site_bottom_nav']);
        $this->assign('site_bottom_child_nav', $GLOBALS['ts']['site_bottom_child_nav']);
        $this->assign('site_nav_apps', $GLOBALS['ts']['site_nav_apps']);
        $this->assign('menuList', $searchSelect);

        return true;
    }


    public function object_array($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }


    /**
     * Curl版本, post方法
     * 使用方法：
     * $post_string = "app=request&version=beta";
     * curl_post('http://facebook.cn/restServer.php',$post_string);
     */
    function curl_post($remote_server, $post_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        /*$return = array('status' => 1, 'data' => $data);
        exit(json_encode($return));*/

        return $data;
    }

    public function curls($url, $timeout = '1000')
    {
        // 1. 初始化
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 3. 执行并获取HTML文档内容
        $info = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);

        return $info;
    }

    /**
     * 解析json串
     * @param type $json_str
     * @return type
     */
    function analyJson($json_str)
    {
        $json_str = str_replace('＼＼', '', $json_str);
        $out_arr = array();
        preg_match('/{.*}/', $json_str, $out_arr);
        if (!empty($out_arr)) {
            $result = json_decode($out_arr[0], TRUE);
        } else {
            return FALSE;
        }
        return $result;
    }

    /** 搜索页面
     */
    public function search()
    {
        $idlist = '';
        if ($_POST['keywork']) {
            $url = 'http://api.luqinwenda.com/s.aspx?key=' . $_POST['keywork'];
            $Result = $this->curls($url);

            $jsonArr = $this->analyJson($Result);

            if (intval($jsonArr['count']) > 0) {
                for ($i = 0; $i < count($jsonArr['items']); $i++) {
                    $idlist .= $jsonArr['items'][$i]['_id'] . ',';
                }
            }
            $this->assign('word', $_POST['keywork']);
        }
        $this->assign('idlist', $idlist);
        $this->display();
    }


    /**
     * 模板变量赋
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的
     * @return void
     */
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->tVar = array_merge($this->tVar, $name);
        } elseif (is_object($name)) {
            foreach ($name as $key => $val)
                $this->tVar[$key] = $val;
        } else {
            $this->tVar[$name] = $value;
        }
    }

    /**
     * 模板显示
     * 调用内置的模板引擎显示方法
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类
     * @return voi
     */
    protected function display($templateFile = '', $charset = 'utf-8', $contentType = 'text/html')
    {
        echo $this->fetch($templateFile, $charset, $contentType, true);
    }

    /**
     *  获取输出页面内容
     * 调用内置的模板引擎fetch方法
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类
     * @return strin
     */
    protected function fetch($templateFile = '', $charset = 'utf-8', $contentType = 'text/html', $display = false)
    {
        $this->assign('appCssList', $this->appCssList);
        $this->assign('langJsList', $this->langJsList);
        Addons::hook('core_display_tpl', array('tpl' => $templateFile, 'vars' => $this->tVar, 'charset' => $charset, 'contentType' => $contentType, 'display' => $display));
        return fetch($templateFile, $this->tVar, $charset, $contentType, $display);
    }

    /**
     * 模板Title
     * @access public
     * @param mixed $input 要
     * @return
     */
    public function setTitle($title = '')
    {
        Addons::hook('core_filter_set_title', $title);
        $this->assign('_title', $title);
    }

    /**
     * 模板keywords
     * @access public
     * @param mixed $input 要
     * @return
     */
    public function setKeywords($keywords = '')
    {
        $this->assign('_keywords', $keywords);
    }


    public function prompt()
    {
        $this->display();
    }

    /**
     * 获取指定用户的某条动态
     *
     * @return void
     */
    public function feed()
    {

        $feed_id = intval($_GET ['feed_id']);

        if (empty($feed_id)) {
            $this->error(L('PUBLIC_INFO_ALREADY_DELETE_TIPS'));
        }

        //获取提问信息
        $feedInfo = model('Feed')->get($feed_id);

        if (!$feedInfo) {
            $this->error('该提问不存在或已被删除');
            exit();
        }

        if ($feedInfo ['is_audit'] == '0') {
            $this->error('此提问正在审核');
            exit();
        }

        if ($feedInfo ['is_del'] == '1') {
            $this->error(L('PUBLIC_NO_RELATE_WEIBO'));
            exit();
        }

        $this->assign('feedInfo', $feedInfo);
        $this->assign('openid', $_GET['openid']);

        $this->setTitle($feedInfo['body']);
        $this->setKeywords($feedInfo['body']);

        $this->display();
    }

    /**
     * 回答
     *
     * @return void
     *
     */
    public function answer()
    {

        $feed_id = intval($_GET ['feed_id']);

        if (empty($feed_id)) {
            $this->error(L('PUBLIC_INFO_ALREADY_DELETE_TIPS'));
        }

        //获取提问信息
        $feedInfo = model('Feed')->get($feed_id);


        if (!$feedInfo) {
            $this->error('该提问不存在或已被删除');
            exit();
        }

        if ($feedInfo ['is_audit'] == '0') {
            $this->error('此提问正在审核');
            exit();
        }

        if ($feedInfo ['is_del'] == '1') {
            $this->error(L('PUBLIC_NO_RELATE_WEIBO'));
            exit();
        }

        $this->assign('feedInfo', $feedInfo);
        $this->assign('openid', $_GET['openid']);

        $this->setTitle($feedInfo['body']);
        $this->setKeywords($feedInfo['body']);

        $this->display();
    }

    /**
     * 首页(卢勤问答平台)
     *
     * @return void
     *
     */
    public function all()
    {
        //安全过滤
        $d['type'] = 'all';
        $d['openid'] = $_GET['openid'];
        $this->assign($d);
        $this->display();
    }

    /**
     * 卢老师的回答
     *
     * @return void
     *
     */
    public function answer_lulaoshi()
    {
        //安全过滤
        $d['type'] = 'lls_answer';
        $d['expert'] = C('TopExpert');
        $d['openid'] = $_GET['openid'];

        $this->assign($d);
        $this->display();
    }

    /**
     * 我的问答
     *
     * @return void
     *
     */
    public function myquestion()
    {
        if (empty($_GET['openid'])) {
            $this->error('参数错误');
        }
        //安全过滤
        $d['type'] = 'question';
        $d['openid'] = $_GET['openid'];

        $this->assign($d);
        $this->display();
    }


    /**
     * 快速提问
     *
     * @return void
     *
     */
    public function quickask()
    {
        if (empty($_GET['openid'])) {
            $this->error('参数错误');
        }
        $d['openid'] = $_GET['openid'];

        $this->assign($d);
        $this->display();
    }


    /**
     * 卢老师管理页面
     *
     * @return void
     *
     */
    public function manage()
    {
        //安全过滤
        $d['type'] = 'manage';
        $d['openid'] = C('TopExpert');

        $this->assign($d);
        $this->display();
    }


    /**
     * 发布提问操作，用于AJAX
     * @return json 发布提问后的结果信息JSON数据
     */
    public function PostFeed()
    {
        // 返回数据格式
        $return = array('status' => 1, 'data' => '');

        // 用户发送内容
        $d['content'] = isset($_POST['content']) ? filter_keyword(h($_POST['content'])) : '';
        // 原始数据内容
        $d['body'] = filter_keyword($_POST['body']);
        // 原始问题描述
        $d['description'] = filter_keyword($_POST['description']);
        // 问题ID
        $d['questionid'] = filter_keyword($_POST['questionid']);
        // 是否追问
        $d['isadd'] = filter_keyword($_POST['addask']);
        $d['inviteid'] = 0;
        if (intval($_POST['inviteid']) > 0) {
            $d['inviteid'] = intval($_POST['inviteid']);
        }

        $d['openid'] = $_POST['Openid'];

        // 安全过滤
        foreach ($_POST as $key => $val) {
            $_POST[$key] = t($_POST[$key]);
        }
        $d['source_url'] = urldecode($_POST['source_url']);  //应用分享到提问，原资源链接
        // 滤掉话题两端的空白
        $d['body'] = preg_replace("/#[\s]*([^#^\s][^#]*[^#^\s])[\s]*#/is", '#' . trim("\${1}") . '#', $d['body']);
        // 附件信息
        $d['attach_id'] = trim(t($_POST['attach_id']), "|");
        if (!empty($d['attach_id'])) {
            $d['attach_id'] = explode('|', $d['attach_id']);
            array_map('intval', $d['attach_id']);
        }
        // 发送提问的类型
        $type = t($_POST['type']);


        // 所属应用名称
        $app = isset($_POST['app_name']) ? t($_POST['app_name']) : APP_NAME;            // 当前动态产生所属的应用
        if (!$data = model('Feed')->put(-1, $app, $type, $d)) {
            $return = array('status' => 0, 'data' => model('Feed')->getError());
            exit(json_encode($return));
        }

        $questionData = model('Feed')->get($d['questionid']);
        if ($questionData) {
            if ($questionData['openid'] != null && $questionData['openid'] != '') {
                $content = '亲爱的用户：你好，有人在卢勤问答平台上回答了你提出的问题“' . $questionData['body'] . '”，快去看看吧！';
                $usermodel = model('user')->getUserInfoByOpenID($questionData['openid']);
                if ($usermodel && !empty($usermodel['source'])) {
                    $this->PostWxUser($questionData['openid'], $questionData['feed_id'], $content, $usermodel['source']);
                }
            }
        }

        // 提问来源设置
        $data ['from'] = getFromClient($data ['from'], $data ['app']);
        $this->assign($data);
        // 提问配置
        $weiboSet = model('Xdata')->get('admin_Config:feed');
        $this->assign('weibo_premission', $weiboSet ['weibo_premission']);

        //填充邀请回答表中的答案ID字段
        if (intval($_POST['inviteid']) > 0) {
            $InviteMap['answerid'] = $data ['feed_id'];

            model('InviteAnswer')->where('invite_answer_id = ' . $_POST['inviteid'])->save($InviteMap);
        }

        // 提问ID
        $return ['feedId'] = $data ['feed_id'];
        $return ['is_audit'] = $data ['is_audit'];
        // 添加话题
        model('FeedTopic')->addTopic(html_entity_decode($d ['body'], ENT_QUOTES), $data ['feed_id'], $type);

        exit(json_encode($return));
    }

    /**
     * 给微信用户发送消息
     * $openid
     * $content  内容
     */
    public function PostWxUser($openid, $feedid, $content, $from)
    {
        //$openid = 'oqrMvt6yRAWFu3DmhGe4Td0nKZRo';//服务号
        //$openid = 'o5jgRt17EmebKpmy6-Wowxvsu9v0';//订阅号
        $fromuser = '';
        $postUrl = '';
        if ($from == 4) //服务号
        {
            $postUrl = 'http://weixin.luqinwenda.com/send_message.aspx';
            $fromuser = 'gh_7c0c5cc0906a';
        } else if ($from == 5) {
            $postUrl = 'http://weixin.luqinwenda.com/dingyue/send_message.aspx';
            $fromuser = 'gh_b8d1e9dcecc8';
        }
        //$param = '{fromuser:"gh_7c0c5cc0906a",touser:"' . $openid . '",msgtype:"text",text:{content:"' . $content . '"}}';
        $param = '{fromuser:"' . $fromuser . '",touser:"' . $openid . '",msgtype:"news",news:{articles: [{title:"亲爱的用户：你好，有人在卢勤问答平台上回答了你提出的问题",description:"' . $content . '",url:"http://www.luqinwenda.com/index.php?app=public&mod=MobileNew&act=feed&feed_id=' . $feedid . '&openid=' . $openid . '",picurl:"http://www.luqinwenda.com/addons/theme/stv1/_static/image/newanswer.jpg"}]}}';

        /*$return = array('status' => 0, 'data' => $postUrl);
        exit(json_encode($return));*/

        $result = $this->curl_post($postUrl, $param);
    }

    /**
     * 操作错误跳转的快捷方
     * @access protected
     * @param string $message 错误信息
     * @param Boolean $ajax 是否为Ajax方
     * @return voi
     */
    protected function error($message)
    {
        $this->_dispatch_jump($message, 0);
    }


    /**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     * @param string $message 提示信息
     * @param Boolean $status 状态
     * @param Boolean $ajax 是否为Ajax方式
     * @access private
     * @return void
     */
    private function _dispatch_jump($message, $status = 1)
    {

        // 提示标题
        $this->assign('msgTitle', $status ? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if ($this->get('closeWin')) $this->assign('jumpUrl', 'javascript:window.close();');
        $this->assign('status', $status);   // 状态
        empty($message) && ($message = $status == 1 ? '操作成功' : '操作失败');
        $this->assign('message', $message);// 提示信息
        //保证输出不受静态缓存影响
        C('HTML_CACHE_ON', false);
        if ($status) { //发送成功信息
            // 成功操作后默认停留1秒
            if (!$this->get('waitSecond')) $this->assign('waitSecond', "2");
            // 默认操作成功自动返回操作前页面
            if (!$this->get('jumpUrl')) $this->assign("jumpUrl", $_SERVER["HTTP_REFERER"]);
            //sociax:2010-1-21
            //$this->display(C('TMPL_ACTION_SUCCESS'));
            $this->display(THEME_PATH . '/success_mobile.html');
        } else {
            //发生错误时候默认停留1秒
            if (!$this->get('waitSecond')) $this->assign('waitSecond', "2");
            // 默认发生错误的话自动返回上页
            if (!$this->get('jumpUrl')) $this->assign('jumpUrl', "javascript:history.back(-1);");
            //sociax:2010-1-21
            //$this->display(C('TMPL_ACTION_ERROR'));

            $this->display(THEME_PATH . '/success_mobile.html');
        }
        if (C('LOG_RECORD')) Log::save();
        // 中止执行  避免出错后继续执行
        exit;
    }

    /**
     * 取得模板显示变量的值
     * @access protected
     * @param string $name 模板显示变量
     * @return mixed
     */
    protected function get($name)
    {
        if (isset($this->tVar[$name]))
            return $this->tVar[$name];
        else
            return false;
    }

    /*public function updateSource()
{
    $map['openid'] = array('exp', ' is not null ');;
    $usermodel = model('user')->where($map)->findAll();
    //print_r(count($usermodel));
    //print_r($usermodel[0]);
    for ($i = 0; $i < count($usermodel); $i++) {
        echo $usermodel[$i]['openid'] . '---------------' . $this->getUserInfo($usermodel[$i]['openid']).'<br />';
    }
}

    function getUserInfo($openid)
    {
        $url = 'http://weixin.luqinwenda.com/getuserinfo.aspx?openid=' . $openid;
        $UserResult = $this->curls($url);
        $jsonUserArr = $this->analyJson($UserResult);
        if (intval($jsonUserArr['errcode']) > 0) {
            return 5;
        }
        return 4;
    }*/
}