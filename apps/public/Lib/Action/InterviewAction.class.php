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

        if(empty($_GET['interview_id']))
        {
            $this->error('参数错误');
            return;
        }

        $InterView = model('Interview')->getInterView('iv_id='.$_GET['interview_id'], 1);
        if (!empty($InterView)) {
            //print_r($InterView['data'][0]);
            $data = $InterView[0];
            $this->setTitle($data['iv_name']);
            $data['iv_startdt'] = strtotime($data['iv_startdt']);
            $data['iv_enddt'] = strtotime($data['iv_enddt']);
            //$this->assign('InterViewState', $this->getState($data['iv_startdt'], $data['iv_enddt']));
            $this->getState($data['iv_startdt'], $data['iv_enddt']);
            $this->assign('InterView', $data);

            $where = " is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND is_audit=1 and interview_audit=1 and publish_time>='"
                . $data['iv_startdt'] . "' AND publish_time < '" . $data['iv_enddt'] . "' and `uid` in (" . $data['iv_object'] . ")";
            $list = model('Feed')->getQuestionAndAnswer($where, 10, 'publish_time desc', false);
            //print_r($list);
            $this->assign('page1', $list['totalPages']);

            $where = " is_audit=1 AND is_del = 0 AND feed_questionid=0 AND add_feedid=0 and interview_audit=1 and publish_time>='" .
                $data['iv_startdt'] . "' AND publish_time < '" . $data['iv_enddt'] . "'";
            $list = model('Feed')->getQuestionAndAnswer($where, 10, 'publish_time desc', false);
            //print_r($list);

            $this->assign('dataCount', $list['count']);
            $this->assign('page2', $list['totalPages']);

        } else {
            $this->error("见面会数据错误");
        }

        //顶级专家
        $expertUid = C('TopExpert');
        $TopExpert = model('user')->getUserInfo($expertUid);
        $this->assign('TopExpert', $TopExpert);

        $this->display();
    }

    private function getState($startdt, $enddt)
    {
        $str = '';
        $stateInt = 0;
        $currentdt = intval(time());
        if ($startdt > $currentdt) {
            $str = '未开始';
            $stateInt = 0;
        } else if ($enddt < $currentdt) {
            $str = '已结束';
            $stateInt = 2;
        } else {
            $str = '进行中...';
            $stateInt = 1;
        }
        $this->assign('InterViewState', $str);
        $this->assign('stateInt', $stateInt);
    }


    public function qafeedlist()
    {
        $flansh = $_GET['flansh'];
        $startdt = intval($_GET['startdt']);
        //echo $startdt;
        //return;
        $enddt = intval($_GET['enddt']);
        $expert = C('TopExpert');
        $limitnums = intval($_GET['limit']) * 10;
        $fuhao = '>=';
        if ($flansh == '1') {
            $fuhao = '>';
        }
        $where = " is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND is_audit=1 and interview_audit=1 and last_updtime " . $fuhao . " '"
            . $startdt . "' AND last_updtime < '" . $enddt . "' and `uid` in (" . $expert . ")";
        //echo $where;
        //return;
        $list = model('Feed')->getAnswerListbyInterview($where, $limitnums . ',10');
        //echo model('Feed')->getLastSql();
        $this->assign('list', $list);
        $dataindex = $_GET['dataindex'];
        $this->assign('dataindex', $dataindex);
        $this->display();
    }

    public function qfeedlist()
    {
        $startdt = intval($_GET['startdt']);
        $enddt = intval($_GET['enddt']);
        $expert = C('TopExpert');
        $limitnums = intval($_GET['limit']) * 10;
        $where = " is_del = 0 AND is_audit=1 AND feed_questionid=0 AND add_feedid=0 AND interview_audit=1 AND publish_time>='"
            . $startdt . "' AND publish_time < '" . $enddt . "'";
        $list = model('Feed')->getQuestionAndAnswerByInterview($where, $limitnums . ',10', 'publish_time desc', false);

        $this->assign('data', $list['data']);
        $this->display();
    }

    public function interviewlist()
    {
        $InterView = model('Interview')->getInterView('', 20);
        //print_r($InterView);
        $this->assign('InterView', $InterView);
        $this->setTitle("卢勤问见面会谈汇总");
        $this->display();
    }
}