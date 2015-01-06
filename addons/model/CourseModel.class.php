<?php
/**
 * 课程模型 - 数据对象模型
 * @author zhangzc
 * @version TS3.0
 */
class CourseModel extends Model {

    protected $tableName = 'course';
    protected $fields =	array(0=>'course_id',1=>'course_title',2=>'course_content',3=>'course_state',4=>'course_dt');

    /**
     * 添加课程
     * @param array $data 课程相关数据
     * @return boolean 是否添加成功
     */
    public function addCourse($data) {
        // 验证数据
        if(empty($data['course_title']) || empty($data['course_content'])) {
            $this->error = '课程标题和内容不能为空';
            return false;
        }

        $data['course_dt'] = time();
        $data['course_state'] = 1;
        if($data['course_id'] = $this->add($data)) {
            // 生成缓存
            model('Cache')->set('course_'.$data['course_id'], $data);
            return true;
        } else {
            $this->error = "课程添加失败";
            return false;
        }
    }

    /**
     * 返回指定ID的课程
     * @param integer $courseID 课程ID
     * @return array 课程资源
     */
    public function getCourseByID($courseID) {
        if(($data = model('Cache')->get('course_'.$courseID)) === false) {
            $map['course_id'] = $courseID;
            $map['course_state'] = 1;
            $data = $this->where($map)->find();
            model('Cache')->set('course_'.$data['course_id'], $data);
        }
        return $data;
    }

    /**
     * 获取课程列表
     * @param array $map 查询条件
     * @param integer $limit 结果集显示数目，默认为10
     * @param string $order 排序条件，默认为course_id DESC
     * @return array 课程列表数据
     */
    public function getCourseList($map, $limit = 10, $order = 'course_id DESC') {
        $list = $this->field('course_id')->where($map)->order($order)->findPage($limit);
        foreach($list['data'] as & $v) {
            $v = $this->getCourseByID($v['course_id']);
        }

        return $list;
    }

}