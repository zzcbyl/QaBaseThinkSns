<?php

/**
 * Created by PhpStorm.
 * User: zhangzc
 * Date: 2015/1/9
 * Time: 11:13
 */
class InterviewAction extends Action
{

    public function index()
    {
        $d['feed_type'] = t($_GET['feed_type']) ? t($_GET['feed_type']) : '';
        $d['feed_key'] = t($_GET['feed_key']) ? t($_GET['feed_key']) : '';
        $this->assign($d);

        $InterView = model('Interview')->getInterView('iv_state=1', 1);
        if (!empty($InterView) && !empty($InterView['data'][0])) {
            //print_r($InterView['data'][0]);
            $data = $InterView['data'][0];
            $data['iv_startdt'] = strtotime($data['iv_startdt']);
            $data['iv_enddt'] = strtotime($data['iv_enddt']);
            $this->assign('InterViewState', $this->getState($data['iv_startdt'], $data['iv_enddt']));
            $this->assign('InterView', $data);

            $where = " is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND is_audit=1 and publish_time>='"
                . $data['iv_startdt'] . "' AND publish_time < '" . $data['iv_enddt'] . "' and `uid` in (" . $data['iv_object'] . ")";
            $list = model('Feed')->getQuestionAndAnswer($where, 10, 'last_updtime desc', false);
            //print_r($list);
            $this->assign('page1', $list['totalPages']);

            $where = " is_audit=1 AND is_del = 0 AND feed_questionid=0 AND add_feedid=0  and last_updtime>='" .
                $data['iv_startdt'] . "' AND last_updtime < '" . $data['iv_enddt'] . "'";
            $list = model('Feed')->getQuestionAndAnswer($where, 1, 'last_updtime desc', false);
            $this->assign('dataCount', $list['count']);

        } else {
            $this->error("访谈数据错误");
        }


        //顶级专家
        $expertUid = C('TopExpert');
        $TopExpert = model('user')->getUserInfo($expertUid);
        $this->assign('TopExpert', $TopExpert);

        $this->display();
    }

    private function getState($startdt, $enddt)
    {
        $startdt = strtotime($startdt);
        $enddt = strtotime($enddt);
        $currentdt = time();
        if ($startdt > $currentdt)
            return '未开始';
        else if ($enddt < $currentdt)
            return '已结束';
        else
            return '进行中...';
    }


    public function qafeedlist()
    {
        $startdt = intval($_GET['startdt']);
        $enddt = intval($_GET['enddt']);
        $expert = C('TopExpert');
        $limitnums = intval($_GET['limit'])*10;
        $where = " is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND is_audit=1 and publish_time>='"
            . $startdt . "' AND publish_time < '" . $enddt . "' and `uid` in (" . $expert . ")";
        $list = model('Feed')->getAnswerListbyInterview($where, $limitnums.',10');

        $this->assign('data', $list['data']);
        $this->display();

        /*if(intval($list['count'])>0) {
            $this->assign('qalastid', $list['data'][(count($list['data']) - 1)]['publish_time']);
            $this->assign('data', $list['data']);
            $this->display();
        }
        else
        {
            return '';
        }*/
    }

}