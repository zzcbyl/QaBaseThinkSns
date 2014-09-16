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
		
		$result 	= model('Passport')->loginLocal($login,$password,0);
		if(!$result){
			$status = 0; 
			$info	= model('Passport')->getError();
			$data 	= 0;
		}else{
			$status = 1;
			$info 	= model('Passport')->getSuccess();
			$data 	= $GLOBALS['mid'];
		}
		
		echo $this->ajaxReturn($data,$info,$status);
	}
	
	
	
	
	
	protected function ajaxReturn($data,$info='',$status=1,$type='JSON') {
		// 保证AJAX返回后也能保存日志
		if(C('LOG_RECORD')) Log::save();
		$result  =  array();
		$result['status']  =  $status;
		$result['info'] =  $info;
		$result['data'] = $data;
		if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
		if(strtoupper($type)=='JSON') {
			// 返回JSON数据格式到客户端 包含状态信息
			header("Content-Type:text/html; charset=utf-8");
			exit(json_encode($result));
		}elseif(strtoupper($type)=='XML'){
			// 返回xml格式数据
			header("Content-Type:text/xml; charset=utf-8");
			exit(xml_encode($result));
		}elseif(strtoupper($type)=='EVAL'){
			// 返回可执行的js脚本
			header("Content-Type:text/html; charset=utf-8");
			exit($data);
		}else{
			// TODO 增加其它格式
		}
	}
}