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
            print_r($InterView['data'][0]);
            $data = $InterView['data'][0];
            $this->assign('InterViewState', $this->getState($data['iv_startdt'], $data['iv_enddt']));
            $this->assign('InterView', $data);

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

}