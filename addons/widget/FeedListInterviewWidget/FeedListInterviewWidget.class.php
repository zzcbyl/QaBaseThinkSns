<?php

/**
 * 提问列表
 * @example {:W('FeedList',array('type'=>'space','feed_type'=>$feed_type,'feed_key'=>$feed_key,'loadnew'=>0,'gid'=>$gid))}
 * @author jason
 * @version TS3.0
 */
class FeedListInterviewWidget extends Widget
{

    private static $rand = 1;
    private $limitnums = 10;

    /**
     * @param string type 获取哪类提问 following:我关注的 space：
     * @param string feed_type 提问类型
     * @param string feed_key 提问关键字
     * @param integer fgid 关注的分组id
     * @param integer gid 群组id
     * @param integer loadnew 是否加载更多 1:是  0:否
     */
    public function render($data)
    {
        $var = array();
        $var['loadmore'] = 1;
        $var['loadnew'] = 1;

        is_array($data) && $var = array_merge($var, $data);
        if($var['type']=='interviewQ') {
            $var['tpl'] = 'FeedList1.html';
        }
        else {
            $var['tpl'] = 'FeedList.html';
        }
        $weiboSet = model('Xdata')->get('admin_Config:feed');
        $var['initNums'] = $weiboSet['weibo_nums'];
        $var['weibo_type'] = $weiboSet['weibo_type'];
        $var['weibo_premission'] = $weiboSet['weibo_premission'];
        // 我关注的频道
        $var['channel'] = M('channel_follow')->where('uid=' . $this->mid)->count();

        // 查询是否有话题ID
        if ($var['topic_id']) {
            $content = $this->getTopicData($var, '_FeedList.html');
        } else {
            $content = $this->getData($var, '_FeedList.html');
        }

        // 查看是否有更多数据
        if (empty($content['html'])) {
            // 没有更多的
            $var['list'] = L('PUBLIC_WEIBOISNOTNEW');
        } else {
            $var['list'] = $content['html'];
            $var['lastId'] = $content['lastId'];
            $var['firstId'] = $content['firstId'] ? $content['firstId'] : 0;
            $var['pageHtml'] = $content['pageHtml'];
        }
        //print($var['lastId']);
        $content['html'] = $this->renderFile(dirname(__FILE__) . "/" . $var['tpl'], $var);

        self::$rand++;
        //unset($var, $data);
        //输出数据
        return $content['html'];
    }

    /**
     * 显示更多提问
     * @return  array 更多提问信息、状态和提示
     */
    public function loadMore()
    {
        // 获取GET与POST数据
        $_REQUEST = $_GET + $_POST;
        // 查询是否有分页
        if (!empty($_REQUEST['p']) || intval($_REQUEST['load_count']) == 4) {
            unset($_REQUEST['loadId']);
            $this->limitnums = 40;
        } else {
            $return = array('status' => -1, 'msg' => L('PUBLIC_LOADING_ID_ISNULL'));
            $_REQUEST['loadId'] = intval($_REQUEST['loadId']);
            $this->limitnums = 10;
        }

        /*if(!empty($_REQUEST['p']) ) {
            unset($_REQUEST['loadId']);
            $this->limitnums = 40;
        } else {
            $return = array('status'=>-1,'msg'=>L('PUBLIC_LOADING_ID_ISNULL'));
            $_REQUEST['loadId'] = intval($_REQUEST['loadId']);
            $this->limitnums = 10;
        }*/
        // 查询是否有话题ID
        if ($_REQUEST['topic_id']) {
            $content = $this->getTopicData($_REQUEST, '_FeedList.html');
        } else {
            $content = $this->getData($_REQUEST, '_FeedList.html');
        }

        /*$return = array('status'=>1,'msg'=>L('PUBLIC_SUCCESS_LOAD'));
        $return['html'] = '||'.json_encode($content).'||';
        exit(json_encode($return));*/

        // 查看是否有更多数据


        if (empty($content['html'])) {
            // 没有更多的
            $return = array('status' => 0, 'msg' => L('PUBLIC_WEIBOISNOTNEW'));
        } else {
            $return = array('status' => 1, 'msg' => L('PUBLIC_SUCCESS_LOAD'));
            $return['html'] = $content['html'];
            $return['loadId'] = $content['lastId'];
            $return['firstId'] = (empty($_REQUEST['p']) && empty($_REQUEST['loadId'])) ? $content['firstId'] : 0;
            $return['pageHtml'] = $content['pageHtml'];
        }
        exit(json_encode($return));
    }

    /**
     * 显示最新提问
     * @return  array 最新提问信息、状态和提示
     */
    public function loadNew()
    {
        $return = array('status' => -1, 'msg' => '');
        $_REQUEST['maxId'] = intval($_REQUEST['maxId']);
        if (empty($_REQUEST['maxId'])) {
            echo json_encode($return);
            exit();
        }
        $content = $this->getData($_REQUEST, '_FeedList.html');
        if (empty($content['html'])) {//没有最新的
            $return = array('status' => 0, 'msg' => L('PUBLIC_WEIBOISNOTNEW'));
        } else {
            $return = array('status' => 1, 'msg' => L('PUBLIC_SUCCESS_LOAD'));
            $return['html'] = $content['html'];
            $return['maxId'] = intval($content['firstId']);
            $return['count'] = intval($content['count']);
        }
        echo json_encode($return);
        exit();
    }

    /**
     * 获取提问数据，渲染提问显示页面
     * @param array $var 提问数据相关参数
     * @param string $tpl 渲染的模板
     * @return array 获取提问相关模板数据
     */
    private function getData($var, $tpl = 'FeedList.html')
    {
        $var['feed_key'] = t($var['feed_key']);
        $var['newcount'] = t($var['newCount']);
        $var['cancomment'] = isset($var['cancomment']) ? $var['cancomment'] : 1;
        //$var['cancomment_old_type'] = array('post','repost','postimage','postfile');
        $var['cancomment_old_type'] = array('post', 'repost', 'postimage', 'postfile', 'weiba_post', 'weiba_repost');
        // 获取提问配置
        $weiboSet = model('Xdata')->get('admin_Config:feed');
        $var = array_merge($var, $weiboSet);
        $var['remarkHash'] = model('Follow')->getRemarkHash($GLOBALS['ts']['mid']);
        $map = $list = array();
        $type = $var['type'];    // 最新的提问与默认提问类型一一对应
        switch ($type) {
            case 'interviewQA': //
                $LoadWhere = '';
                if ($var['loadId'] > 0) { //非第一次
                    $LoadWhere = "AND last_updtime < '" . intval($var['loadId']) . "'";
                } else {
                    $LoadWhere = "AND last_updtime < '" . $var['enddt'] . "'";
                }
                $where = " is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND is_audit=1 and last_updtime>='" . $var['startdt'] . "' " . $LoadWhere . " and `uid` in (" . $var['expert'] . ")";
                $list = model('Feed')->getAnswerList($where, $this->limitnums);
                //print_r(model('Feed')->getLastSql());
                break;
            case 'interviewQ': //所有的 --正在发生的
                $LoadWhere='';
                if ($var['loadId'] > 0) { //非第一次
                    $LoadWhere = "AND last_updtime < '" . intval($var['loadId']) . "'";
                } else {
                    $LoadWhere = "AND last_updtime < '" . $var['enddt'] . "'";
                }
                $where = " is_audit=1 AND is_del = 0 AND feed_questionid=0 AND add_feedid=0  and last_updtime>='" . $var['startdt'] . "' " . $LoadWhere ;
                $list = model('Feed')->getQuestionAndAnswer($where, $this->limitnums,'last_updtime desc',false);
                //print($where);
                break;
        }
        // 分页的设置
        isset($list['html']) && $var['html'] = $list['html'];


        $feedlist = array();
        if (!empty($list['data'])) {
            switch ($type) {
                case 'interviewQA':
                    $content['firstId'] = $var['firstId'] = $list['data'][0]['last_updtime'];
                    $content['lastId'] = $var['lastId'] = $list['data'][(count($list['data']) - 1)]['last_updtime'];
                    break;
                case 'interviewQ':
                    $content['firstId'] = $var['firstId'] = $list['data'][0]['last_updtime'];
                    $content['lastId'] = $var['lastId'] = $list['data'][(count($list['data']) - 1)]['last_updtime'];
                    break;
                default:
                    $content['firstId'] = $var['firstId'] = $list['data'][0]['publish_time'];
                    $content['lastId'] = $var['lastId'] = $list['data'][(count($list['data']) - 1)]['publish_time'];
                    break;
            }

            if (count($feedlist) > 0) //关注问题的数据
                $var['data'] = $feedlist['data'];
            else
                $var['data'] = $list['data'];

            //赞功能
            $feed_ids = getSubByKey($var['data'], 'feed_id');
            $var['diggArr'] = model('FeedDigg')->checkIsDigg($feed_ids, $GLOBALS['ts']['mid']);

            $uids = array();
            foreach ($var['data'] as &$v) {
                switch ($v['app']) {
                    case 'weiba':
                        $v['from'] = getFromClient(0, $v['app'], '微吧');
                        break;
                    case 'tipoff':
                        $v['from'] = getFromClient(0, $v['app'], '爆料');
                        break;
                    default:
                        $v['from'] = getFromClient($v['from'], $v['app']);
                        break;
                }
                !isset($uids[$v['uid']]) && $v['uid'] != $GLOBALS['ts']['mid'] && $uids[] = $v['uid'];
            }
            if (!empty($uids)) {
                $map = array();
                $map['uid'] = $GLOBALS['ts']['mid'];
                $map['fid'] = array('in', $uids);
                $var['followUids'] = model('Follow')->where($map)->getAsFieldArray('fid');
            } else {
                $var['followUids'] = array();
            }
        }

        //print_r($var);

        $content['pageHtml'] = $list['html'];
        //print(dirname(__FILE__));
        // 渲染模版

        $loginData = model('Login')->get($GLOBALS['ts']['mid'], 'sina');
        if ($loginData['oauth_token'] != '') {
            $var['token'] = '1';
        }

        $loginData = model('Login')->get($GLOBALS['ts']['mid'], 'qzone');
        if ($loginData['oauth_token'] != '') {
            $var['qqtoken'] = '1';
        }

        $content['html'] = $this->renderFile(dirname(__FILE__) . "/" . $tpl, $var);

        return $content;
    }

    /**
     * 获取话题提问数据，渲染提问显示页面
     * @param array $var 提问数据相关参数
     * @param string $tpl 渲染的模板
     * @return array 获取提问相关模板数据
     */
    private function getTopicData($var, $tpl = 'FeedList.html')
    {
        $var['cancomment'] = isset($var['cancomment']) ? $var['cancomment'] : 1;
        //$var['cancomment_old_type'] = array('post','repost','postimage','postfile');
        $var['cancomment_old_type'] = array('post', 'repost', 'postimage', 'postfile', 'weiba_post', 'weiba_repost');
        $weiboSet = model('Xdata')->get('admin_Config:feed');
        $var = array_merge($var, $weiboSet);
        $var['remarkHash'] = model('Follow')->getRemarkHash($GLOBALS['ts']['mid']);
        $map = $list = array();
        $type = $var['new'] ? 'new' . $var['type'] : $var['type'];    //最新的提问与默认提问类型一一对应

        if ($var['loadId'] > 0) { //非第一次
            $topics['topic_id'] = $var['topic_id'];
            $topics['feed_id'] = array('lt', intval($var['loadId']));
            $map['feed_id'] = array('in', getSubByKey(D('feed_topic_link')->where($topics)->field('feed_id')->select(), 'feed_id'));
        } else {
            $map['feed_id'] = array('in', getSubByKey(D('feed_topic_link')->where('topic_id=' . $var['topic_id'])->field('feed_id')->select(), 'feed_id'));
        }
        if (!empty($var['feed_type'])) {
            $map['type'] = t($var['feed_type']);
        }
        //$map['is_del'] = 0;
        $map['_string'] = ' (is_audit=1 OR is_audit=0 AND uid=' . $GLOBALS['ts']['mid'] . ') AND is_del = 0 ';
        $list = model('Feed')->getList($map, $this->limitnums);
        //分页的设置
        isset($list['html']) && $var['html'] = $list['html'];

        if (!empty($list['data'])) {
            $content['firstId'] = $var['firstId'] = $list['data'][0]['feed_id'];
            $content['lastId'] = $var['lastId'] = $list['data'][(count($list['data']) - 1)]['feed_id'];
            $var['data'] = $list['data'];

            //赞功能
            $feed_ids = getSubByKey($var['data'], 'feed_id');
            $var['diggArr'] = model('FeedDigg')->checkIsDigg($feed_ids, $GLOBALS['ts']['mid']);

            $uids = array();
            foreach ($var['data'] as &$v) {
                switch ($v['app']) {
                    case 'weiba':
                        $v['from'] = getFromClient(0, $v['app'], '微吧');
                        break;
                    default:
                        $v['from'] = getFromClient($v['from'], $v['app']);
                        break;
                }
                !isset($uids[$v['uid']]) && $v['uid'] != $GLOBALS['ts']['mid'] && $uids[] = $v['uid'];
            }
            if (!empty($uids)) {
                $map = array();
                $map['uid'] = $GLOBALS['ts']['mid'];
                $map['fid'] = array('in', $uids);
                $var['followUids'] = model('Follow')->where($map)->getAsFieldArray('fid');
            } else {
                $var['followUids'] = array();
            }
        }

        $content['pageHtml'] = $list['html'];

        //渲染模版
        $content['html'] = $this->renderFile(dirname(__FILE__) . "/" . $tpl, $var);

        return $content;
    }

    /**
     * 获取微吧帖子数据
     * @param  [varname] [description]
     */
    public function getPostDetail()
    {
        $post_id = intval($_POST['post_id']);
        $post_detail = D('weiba_post')->where('is_del=0 and post_id=' . $post_id)->find();
        if ($post_detail && D('weiba')->where('is_del=0 and weiba_id=' . $post_detail['weiba_id'])->find()) {
            $post_detail['post_url'] = U('weiba/Index/postDetail', array('post_id' => $post_id));
            $author = model('User')->getUserInfo($post_detail['post_uid']);
            $post_detail['author'] = $author['space_link'];
            $post_detail['post_time'] = friendlyDate($post_detail['post_time']);
            $post_detail['from_weiba'] = D('weiba')->where('weiba_id=' . $post_detail['weiba_id'])->getField('weiba_name');
            $post_detail['weiba_url'] = U('weiba/Index/detail', array('weiba_id' => $post_detail['weiba_id']));
            return json_encode($post_detail);
        } else {
            echo 0;
        }
    }

    public function getTipoffDetail()
    {
        $tipoff_id = intval($_POST['tipoff_id']);
        $tipoff_detail = D('tipoff')->where('deleted=0 and archived=0 and tipoff_id=' . $tipoff_id)->find();
        if ($tipoff_detail) {
            $tipoff_detail['tipoff_url'] = U('tipoff/Index/detail', array('id' => $tipoff_id));
            $author = model('User')->getUserInfo($tipoff_detail['uid']);
            $tipoff_detail['author'] = $author['space_link'];
            $tipoff_detail['publish_time'] = friendlyDate($tipoff_detail['publish_time']);
            $tipoff_detail['from_category'] = D('tipoff_category')->where('tipoff_category_id=' . $tipoff_detail['category_id'])->getField('title');
            $tipoff_detail['category_url'] = U('tipoff/Index/index', array('cid' => $tipoff_detail['category_id']));
            return json_encode($tipoff_detail);
        } else {
            echo 0;
        }
    }

}