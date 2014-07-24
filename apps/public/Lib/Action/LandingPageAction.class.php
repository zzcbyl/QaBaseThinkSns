<?php
/**
* 登录验证页面
* @author liuxiaoqing <liuxiaoqing@zh
* @version TS3.0
*/
class LandingPageAction
{
	public function Landing()
	{
		//echo urlencode('http://localhost/index.php?app=public&mod=Mobile&act=all&type=invite').'<br />';
		$openid = $_GET['openid'];
		$dt = $_GET['time'];
		$url = $_GET['url'];
		$code = $_GET['code'];
		$source = $_GET['source'];
		
		if(empty($openid) || empty($dt) || empty($code)) {
			echo '非法访问';
			return;
		}
		//判断时间
		$date = date("Y-m-d H:i:s",strtotime("-30 minute"));
		echo time().'<br>';
		if($dt > time() || $dt < strtotime($date))
		{
			echo '访问超时';
			return;
		}
		//判断code
		$key = C('WXURL_KEY');
		echo md5($openid.$dt.$key).'<br>';
		if(md5($openid.$dt.$key) != $code)
		{
			echo '非法访问';
			return;
		}
		//判断openid存在去登录,否则去注册
		$user = model('User')->getUserInfoByOpenID($openid);
		//print_r($user);
		
		//echo $url;
		//return;
		
		if(!empty($user))
		{
			$result = model('Passport')->loginLocalWhitoutPassword($openid);
			if(empty($url))
			{
				$this->redirect('public/Mobile/all');
			}
			else
			{
				redirect($url);
			}
		}
		else
		{
			if(empty($url))
			{
				$this->redirect('public/Register/Homemobile', array('openid'=>$openid,'source'=>$source));
			}
			else
			{
				redirect($url);
			}
		}
	}
	/**
	* Action跳转(URL重定向） 支持指定模块和延时跳转
	* @access protected
	* @param string $url 跳转的URL表达式
	* @param array $params 其它URL参数
	* @param integer $delay 延时跳转的时间 单位为秒
	* @param string $msg 跳转提示信息
	* @return void
	*/
	protected function redirect($url,$params=array(),$delay=0,$msg='') {
		if(C('LOG_RECORD')) Log::save();
		$url    =   U($url,$params);
		redirect($url,$delay,$msg);
	}
}