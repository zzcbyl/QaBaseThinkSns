<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/1/9
 * Time: 15:53
 */
class InterviewModel extends Model
{

    protected $tableName = 'interview';
    protected $fields = array('iv_id', 'iv_name', 'iv_content', 'iv_startdt', 'iv_enddt', 'iv_state', 'iv_object', 'iv_crt', '_pk' => 'vi_id');

    public function addInterView($map = array())
    {
        if (empty($map['iv_name'])) {
            $this->error = '访谈名称不能为空';
            return false;
        }
        if (empty($map['iv_startdt'])) {
            $this->error = '访谈开始时间不能为空';
            return false;
        }
        if (empty($map['iv_enddt'])) {
            $this->error = '访谈结束时间不能为空';
            return false;
        }
        if (empty($map['iv_object'])) {
            $this->error = '访谈对象不能为空';
            return false;
        }
        if (empty($map['iv_state'])) {
            $map['iv_state'] = 1;
        }

        $iv_id = $this->add($map);
        if (!$iv_id) {
            $this->error = '添加失败';
            return false;
        }

        return true;
    }


    public function getInterView($where = '', $limit = 20, $format = true)
    {
        $list = D('interview')->where($where)->order('iv_id desc')->findpage($limit);
        //数据格式化
        if($format) {
            foreach ($list['data'] as $k => $v) {
                $v['iv_startdt'] = date('Y-m-d H:i:s', $v['iv_startdt']);
                $v['iv_enddt'] = date('Y-m-d H:i:s', $v['iv_enddt']);
                $v['iv_state'] = $v['iv_state'] == 1 ? '正常' : '已结束';
                $v['iv_crt'] = date('Y-m-d H:i:s', $v['iv_crt']);
                $list['data'][$k] = $v;
            }
        }
        return $list;
    }



}