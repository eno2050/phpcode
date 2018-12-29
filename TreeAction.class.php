<?php
/**
 * 无限极分类控制器
 * 
 */

class TreeAction extends Action {

	//所有会员数据
	private $data;
	//获取下级会员数组
	private $downMemberArr;
	//获取上级会员数组
	private $upMemberArr;
	//会员uid
	private $uid;
	//会员pid
	private $pid;
	//会员表名
	private $tableName;
	//会员id字段
	private $uidField;
	//会员父级字段
	private $pidField;
	//母号id
	private $motherUid = 11112;



	/**
	 * [_initialize description]
	 * @Author   eno2050
	 * @DateTime 2018-12-25T17:28:25+0800
	 * @param    [int] $uid [用户uid]
	 * @return   初始化方法
	 * _initialize 这个方法在tp源码里面没有传值 导致错误
	 */
	public function __construct($uid,$tableName,$userField,$pidField){
		//初始化属性
		$this->uid = $uid;
		$this->tableName = $tableName;
		$this->uidField = $userField;
		$this->pidField = $pidField;
		$this->pid = M($tableName)->where($this->uidField.'='.$uid)->getField($this->pidField);
		$this->data = M($tableName)->select();
		$this->downMemberArr = $this->getDownMemberArr($this->data, $this->uid);

	}


	/**
	 * 获取下级所有会员
	 * @Author   Eno2050
	 * @DateTime 2018-12-25T17:53:06+0800
	 * @param    [type]                   $data  [所有会员数据]
	 * @param    [type]                   $uid   [用户uid]
	 * @param    integer                  $level [标记会员层级]
	 * @return   [array]                         [按会员层级为键值的数组]
	 */
	private function getDownMemberArr($data, $uid,$level = 0){

		static $tree = array();
		foreach($data as $row) {
		    if($row[$this->pidField] == $uid) {
		        $tree[$level][] = $row;
		        $this->getDownMemberArr($data, $row[$this->uidField], $level + 1);
		    }
		}
		return $tree;

	}
	/**
	 * 获取N代会员
	 * @Author   eno2050
	 * @DateTime 2018-12-25T18:03:39+0800
	 * @param    [int]                   $level [层级]
	 * @return   [array]                          [会员数组]
	 */
	public function getDownMemberArr2($level = 9){

		$arr = array_slice($this->downMemberArr,0,$level);
		return $arr;

	}

	/**
	 * 获取上级会员父级数组
	 * @author   eno2050
	 * @DateTime 2018-12-25T18:19:17+0800
	 * @param    [array]                 $n [获取多少上级]
	 * @return   [array]                 [一维数组，所有的迭代父级]
	 */
	public function getUpMemberArr($n = 1){

		if(!isset($n)){
			$n = 1;
		}
		$arrpid = $this->pid;

		for ($i=1; $i<=$n; $i++) {
            $map[$this->uidField] = array('in',$arrpid);
            $result = M($this->tableName)->where($map)->find();
            $arrpid = $result[$this->pidField];
            $userid = $result[$this->uidField];
            $userList[$i] = $userid;
            if($this->motherUid == $userid){
            	break;
            }
        }

        return $userList;

	}


	/**
	当给一个无访问权限或不存在的属性赋值时，__set()魔术方法会自动调用，并传入两个参数：属性和属性值
	$suzhou->_cityName='苏州' ---无权访问权限--> __set('_cityName','苏州');
	**/
	public function __set($property_name,$property_value){
		$this->$property_name = $property_value;
	}
	
	/**
	当我们调用一个权限上不允许访问的属性或者是不存在的属性时，__get()魔术方法会自动调用，并且自动传参，参数名是属性名
	$suzhou->_cityName ---无访问权限--> __get(_cityName);
	**/
	public function __get($property_name){
		if(isset($this->$property_name)){
			return $this->$property_name;	
		}else{
			return null;
		}
		
	}

}


