<?php
/**
 * 用户教育信息模型 - 数据对象模型
 * @author zhangzc
 * @version TS3.0
 */
class UserEducationModel extends Model {
	protected $tableName = 'user_education  ';
	protected $fields = array(0=>'id',1=>'uid',2=>'schooltype',3=>'schoolname',4=>'entertime',5=>'departments',6=>'permissions');
	
	
	/**
	* 添加指定用户的教育信息
	* @param array $data(key=>value) 用户档案信息
	* @return boolean 是否保存成功
	*/
	public function addUserEducation($data) {
		return $this->add($data);;
	}
	
	/**
	* 修改指定id的教育信息
	* @param integer $id ID
	* @param array $data(key=>value) 用户档案信息
	* @return boolean 是否保存成功
	*/
	public function updUserEducation($id, $map) {
		return $this->where('id='.$id)->save($map);
	}
	
	/**
	* 删除指定id的教育信息
	* @param integer $id ID
	* @return boolean 是否保存成功
	*/
	public function delUserEducation($id) {
		$this->where('id='.$id)->delete();	
	}
	
	/**
	* 获取指定用户的教育信息
	* @param integer $uid 用户UID
	* @param integer $fid 查看用户ID
	* @return array $data
	*/
	public function getUserEducationList($uid,$fid = 0) {
		$where = ' uid = '.$uid;
		if($fid!=0)
		{
			$State = model('Follow')->getFollowState($uid,$fid);
			if(is_array($State) && $State['following']==1)
				$where .= ' and (permissions = 0 or permissions = 1) ';
			else
				$where .= ' and permissions = 0 ';
		}
		$data = $this->where($where)->order('id DESC')->select();
		return $data;
	}
	
	
	
	
}