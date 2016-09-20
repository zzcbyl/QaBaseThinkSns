<?php
/**
 * 用户职业信息模型 - 数据对象模型
 * @author zhangzc
 * @version TS3.0
 */
class UserProfessionModel extends Model {
	protected $tableName = 'user_profession ';
	protected $fields = array(0=>'id',1=>'uid',2=>'company',3=>'position',4=>'position',5=>'worktime',6=>'location',7=>'province',8=>'city',9=>'area',10=>'permissions');
	
	
	/**
	* 添加指定用户的职业信息
	* @param array $data(key=>value) 用户档案信息
	* @return boolean 是否保存成功
	*/
	public function addUserProfession($data) {
		return $this->add($data);;
	}
	
	/**
	* 修改指定id的职业信息
	* @param integer $id ID
	* @param array $data(key=>value) 用户档案信息
	* @return boolean 是否保存成功
	*/
	public function updUserProfession($id, $map) {
		return $this->where('id='.$id)->save($map);
	}
	
	/**
	* 删除指定id的职业信息
	* @param integer $id ID
	* @return boolean 是否保存成功
	*/
	public function delUserProfession($id) {
		$this->where('id='.$id)->delete();	
	}
	
	/**
	* 获取指定用户的职业信息
	* @param integer $uid 用户UID
	* @param integer $fid 查看用户ID
	* @return array $data
	*/
	public function getUserProfessionList($uid,$fid = 0) {
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