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
		if($user)
			echo json_encode($user);
		else
			echo 0;
	}

}