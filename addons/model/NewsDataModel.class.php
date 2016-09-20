<?php
/**
 * 用户关联资料模型 - 数据对象模型
 * @author zhangzc
 * @version TS3.0
 */
class NewsDataModel extends Model {
	protected $tableName = 'newsdata  ';
	protected $fields = array('newsdata_id','uid','title','description','type','ctime','_pk'=>'newsdata_id');
	
	
	/**
	* 添加指定用户的关联资料
	* @param array $data(key=>value) 资料信息
	* @return boolean 是否保存成功
	*/
	public function addData($data) {
		return $this->add($data);;
	}
	
	/**
	* 修改指定id的资料信息
	* @param integer $dataid ID
	* @param array $data(key=>value) 用户资料信息
	* @return boolean 是否保存成功
	*/
	public function updNewsData($dataid, $map) {
		return $this->where('newsdata_id='.$dataid)->save($map);
	}
	
	/**
	* 删除指定id的资料
	* @param integer $dataid ID
	* @return boolean 是否删除成功
	*/
	public function delNewsData($dataid) {
		$this->where('newsdata_id='.$dataid)->delete();	
	}
	
	/**
	* 获取指定条件的资料
	* @param integer $where 条件
	* @return array $data
	*/
	public function getNewDataList($where, $limit = 10, $order=' newsdata_id desc') {
		
		$data = $this->where($where)->order($order)->findPage($limit);
		return $data;
	}
	
	
	
	
}