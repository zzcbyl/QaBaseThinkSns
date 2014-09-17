<?php
/**
 * 对外接口
 * @author zhangzc
 * @version TS3.0
 */
class MyApiAction 
{
	/**
	 * 登录
	 *
	 * @return JSON
	 *
	 */	
	public function Login()
	{
		$login 		= t($_GET['loginusername']);
		$password 	= trim($_GET['loginpassword']);
		if($login == '用户名')
			$login = '';
		if($password == '******')
			$password = '';
		
		$user 	= model('Passport')->getLocalUser($login,$password);
		$result=array();
		$resule['uid']=$user['uid'];
		$resule['login']=$user['login'];
		$resule['uname']=$user['uname'];
		$resule['email']=$user['email'];
		$resule['sex']=$user['sex'];
		$resule['location']=$user['location'];
		$resule['is_del']=$user['is_del'];
		if($user)
			echo json_encode($result);
		else
			echo 0;
	}

}