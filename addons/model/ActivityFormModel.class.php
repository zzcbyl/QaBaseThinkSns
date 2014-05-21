<?php
/**
 * 第三方关联数据 - 活动报名数据模型
 * @author zhangzc
 * @version TS3.0
 */
class ActivityFormModel extends Model {

	protected $tableName = 'activity_form';
	protected $fields = array('activity_form_id','childname','childage','childsex','childheight','childminzu','parentsname1','parentsex1','parentheight1','parentminzu1','parentsmobile1','parentsemail1','parentsname2','parentsex2','parentheight2','parentminzu2','parentsmobile2','parentsemail2','istogether','remarks','ctime','activityname','_pk'=>'activity_form_id');
	
	/**
	 * 获取指定的关联数据
	 * @param integer $uid 用户ID
	 * @param integer $type 类型ID
	 * @return
	 */
	public function getList($childname='',$istogether=null,$order='ctime desc',$limit=20,$name='') {
		if($name!='')
			$map['activityname'] = $name;
		if($childname!='')
			$map['childname'] = $childname;
		if($istogether!=null)
			$map['istogether'] = $istogether;
		$data = $this->where($map)->order($order)->findPage($limit);
		return $data;
	}
}