<?php
/**
 * 第三方关联数据 - 数据对象模型
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */
class LoginModel extends Model {

	protected $tableName = 'login';
	protected $fields = array('login_id','uid','type_uid','type','oauth_token','oauth_token_secret','is_sync','_pk'=>'login_id');

	
	/**
	 * 获取指定的关联数据
	 * @param integer $uid 用户ID
	 * @param integer $type 类型ID
	 * @return
	 */
	public function get($uid, $type = 'sina') {
		$map['uid'] = $uid;
		$map['type'] = $type;
		$data = $this->where($map)->find();
		
		return $data;
	}
		
}