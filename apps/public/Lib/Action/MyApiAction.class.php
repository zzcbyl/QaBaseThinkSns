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
		$result['uid']=$user['uid'];
		$result['login']=$user['login'];
		$result['uname']=$user['uname'];
		$result['email']=$user['email'];
		$result['sex']=$user['sex'];
		$result['location']=$user['location'];
		$result['is_del']=$user['is_del'];
		if($user)
			echo json_encode($result);
		else
			echo 0;
	}

}