<?php
/**
 * 提问列表
 * @example {:W('UserBaseInfo',array('uid'=>$uid))}
 * @author jason
 * @version TS3.0
 */
class UserBaseInfoWidget extends Widget {

	/**
	 * @param string type 获取哪类提问 following:我关注的 space：
	 * @param string feed_type 提问类型
	 * @param string feed_key 提问关键字
	 * @param integer fgid 关注的分组id
	 * @param integer gid 群组id
	 * @param integer loadnew 是否加载更多 1:是  0:否
	 */
	public function render($data) {
		$uid = intval ( $data ['uid'] );
		
		$var =	model('User')->getUserInfo($uid);
		$var['mid'] = intval ( $data ['mid'] );
		
		$this->_config = model('Xdata')->get('admin_Config:register');
		$var['config']=$this->_config;
		
		$userper = model('UserPermissions')->getUserPermissions($uid);
		$var['permissions'] = $userper;
		//print_r($userper);
		$var['followstate'] = $data['followstate'];
		//print_r($var);
		
		//print_r($var);
		// 渲染模版
		$content = $this->renderFile(dirname(__FILE__)."/baseinfo.html", $var);
		// 输出数据
		return $content;
	}
	
	
}