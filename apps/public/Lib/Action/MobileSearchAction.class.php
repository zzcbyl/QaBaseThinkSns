<?php
/**
 * SearchAction 搜索模块
 * @version TS3.0
 */
class MobileSearchAction
{
	/**
	    * 架构函数 取得模板对
	    * @access public
	    */
	public function __construct() {
		$this->initSite();

	}
	/**
	* 站点信息初始化
	* @access private
	* @return void
	*/
	private function initSite() {
		
		//载入站点配置全局变量
		$this->site = model('Xdata')->get('admin_Config:site');

		if($this->site['site_closed'] == 0 && APP_NAME !='admin'){
			//TODO  跳转到站点关闭页面
			$this->page404($this->site['site_closed_reason']); exit();
		}
		
		//检查是否启用rewrite
		if(isset($this->site['site_rewrite_on'])){
			C('URL_ROUTER_ON',($this->site['site_rewrite_on']==1));
		}

		//初始化语言包
		$cacheFile = C('F_CACHE_PATH').'/initSiteLang.lock.php';
		if(!file_exists($cacheFile)){
			model('Lang')->initSiteLang();
		}        
		
		//LOGO处理
		$this->site['logo'] = getSiteLogo($this->site['site_logo']);
		
		//默认登录后首页
		if(intval($this->site['home_page'])){
			$appInfo = model('App')->where('app_id='.intval($this->site['home_page']))->find();
			$this->site['home_url'] = U($appInfo['app_name'].'/'.$appInfo['app_entry']);
		}else{
			$this->site['home_url'] = U('public/Index/index');
		}

		//赋值给全局变量
		$GLOBALS['ts']['site'] = $this->site;

		//网站导航
		$GLOBALS['ts']['site_top_nav'] = model('Navi')->getTopNav();
		$GLOBALS['ts']['site_bottom_nav'] = model('Navi')->getBottomNav();
		$GLOBALS['ts']['site_bottom_child_nav'] = model('Navi')->getBottomChildNav($GLOBALS['ts']['site_bottom_nav']);

		//获取可搜索的内容列表
		if(false===($searchSelect=S('SearchSelect'))){
			$searchSelect = D('SearchSelect')->findAll();
			S('SearchSelect',$searchSelect);
		}
		
		//网站所有的应用
		$GLOBALS['ts']['site_nav_apps'] = model('App')->getAppList(array('status'=>1,'add_front_top'=>1),9);

		//网站全局变量过滤插件
		Addons::hook('core_filter_init_site');

		$this->assign('site', $this->site);
		$this->assign('site_top_nav', $GLOBALS['ts']['site_top_nav']);
		$this->assign('site_bottom_nav', $GLOBALS['ts']['site_bottom_nav']);
		$this->assign('site_bottom_child_nav',$GLOBALS['ts']['site_bottom_child_nav']);
		$this->assign('site_nav_apps', $GLOBALS['ts']['site_nav_apps']);
		$this->assign('menuList', $searchSelect);

		return true;
	}

	
	public function object_array($array) {  
		if(is_object($array)) {  
			$array = (array)$array;  
		} if(is_array($array)) {  
			foreach($array as $key=>$value) {  
				$array[$key] = $this->object_array($value);  
			}  
		}  
		return $array;  
	} 
	
	public function curls($url, $timeout = '1000')
	{
		// 1. 初始化
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 3. 执行并获取HTML文档内容
		$info = curl_exec($ch);
		// 4. 释放curl句柄
		curl_close($ch);

		return $info;
	}
	
	/**
	* 解析json串
	* @param type $json_str
	* @return type
	*/
	function analyJson($json_str) {
		$json_str = str_replace('＼＼', '', $json_str);
		$out_arr = array();
		preg_match('/{.*}/', $json_str, $out_arr);
		if (!empty($out_arr)) {
			$result = json_decode($out_arr[0], TRUE);
		} else {
			return FALSE;
		}
		return $result;
	}
	
	/** 搜索页面
	*/
	public function search()
	{
		$idlist = '';
		if($_POST['keywork'])
		{
			$url = 'http://api.luqinwenda.com/s.aspx?key='.$_POST['keywork'];
			$Result = $this->curls($url);
			
			$jsonArr = $this->analyJson($Result);
			
			if(intval($jsonArr['count'])>0)
			{
				for($i = 0; $i < count($jsonArr['items']); $i++)
				{
					$idlist .= $jsonArr['items'][$i]['_id'].',';
				}
			}
			$this->assign ( 'word', $_POST['keywork'] );
		}
		$this->assign ( 'idlist', $idlist );
		$this->display ();
	}
	
	
	/**
	    * 模板变量赋
	    * @access protected
	    * @param mixed $name 要显示的模板变量
	    * @param mixed $value 变量的
	    * @return void
	    */
	public function assign($name,$value='') {
		if(is_array($name)) {
			$this->tVar   =  array_merge($this->tVar,$name);
		}elseif(is_object($name)){
			foreach($name as $key =>$val)
				$this->tVar[$key] = $val;
		}else {
			$this->tVar[$name] = $value;
		}
	}
	
	/**
	* 模板显示
	* 调用内置的模板引擎显示方法
	* @access protected
	* @param string $templateFile 指定要调用的模板文件
	* 默认为空 由系统自动定位模板文件
	* @param string $charset 输出编码
	* @param string $contentType 输出类
	* @return voi
	*/
	protected function display($templateFile='',$charset='utf-8',$contentType='text/html') {
		echo $this->fetch($templateFile,$charset,$contentType,true);
	}
	
	/**
	*  获取输出页面内容
	* 调用内置的模板引擎fetch方法
	* @access protected
	* @param string $templateFile 指定要调用的模板文件
	* 默认为空 由系统自动定位模板文件
	* @param string $charset 输出编码
	* @param string $contentType 输出类
	* @return strin
	*/
	protected function fetch($templateFile='',$charset='utf-8',$contentType='text/html',$display=false) {
		$this->assign('appCssList',$this->appCssList);
		$this->assign('langJsList', $this->langJsList);
		Addons::hook('core_display_tpl', array('tpl'=>$templateFile,'vars'=>$this->tVar,'charset'=>$charset,'contentType'=>$contentType,'display'=>$display));
		return fetch($templateFile, $this->tVar, $charset, $contentType, $display);
	}
	
	/**
	* 模板Title
	* @access public
	* @param mixed $input 要
	* @return
	*/
	public function setTitle($title = '') {
		Addons::hook('core_filter_set_title', $title);
		$this->assign('_title',$title);
	}
}