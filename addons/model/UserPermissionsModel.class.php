<?php
/**
 * 用户档案模型 - 数据对象模型
 * @author jason <yangjs17@yeah.net> 
 * @version TS3.0
 */
class UserPermissionsModel	extends	Model {
	protected $tableName = 'user_permissions';
	protected $fields = array(0=>'uid',1=>'key',2=>'value');
	
	
	 /**
	 * 保存指定用户字段权限
	 * @param integer $uid 用户UID
	 * @param array $data(key=>value) 用户档案信息
	 * @return boolean 是否保存成功
	 */
	public function saveUserPermissions($uid, $data) {
		foreach	($data as $k=>$v)
		{
			$map['uid'] = $uid;
			$map['key'] = $k;
			if($v==0) //所有人可见
			{
				$this->where($map)->delete();
			}
			else
			{		
				$checkdata = $this->where($map)->select();
				if(count($checkdata)>0)
				{
					$m['value']=$v;
					$this->where($map)->save($m);
				}
				else
				{
					$map['value']=$v;
					$this->add($map);
				}
			}
		}
	}
	
	/**
	* 获取指定用户的字段权限
	* @param integer $uid 用户UID
	* @return array $data
	*/
	public function getUserPermissions($uid) {
		$map['uid'] = $uid;
		$data = $this->where($map)->select();
		$dataList=array();
		foreach	($data as $k=>$v)
		{
			$dataList[$v['key']]=$v['value'];
		}
		return $dataList;
	}
	
}