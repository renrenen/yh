<?php

namespace app\user\controller;

use think\facade\View;
use app\BaseController;
use app\admin\model\Information;
use app\admin\model\Record;

/**
 * note:用户后台
 */
class Index extends BaseController
{

	public function index()
	{
            if($this->userinfo['type']==1){
            	$b = $this->userinfo['surplus'];
            	if($b<=100){
            	    $b = '<font color="red">'.$b.'</font>';
            	}
            }else{
            	$time = $this->userinfo['surplus'];
            	$b = timeDiff(time(),$time);
            	$b = $b['day'];
            	if($b<=2){
            	    $b = '<font color="red">'.$b.'</font>';
            	}
            }
            $time = date('Y-m-d');
            $utime = strtotime($time);
            $num = Record::where('uid',$this->userinfo['user_uid'])->where('intime','>',$utime)->count();
            $Information = Information::find(1);
			view::assign([
				'title'		=> '用户首页',
				'notice'	=> $Information->notice,
	          	'daydy' 	=> $num,  //今日调用
	          	'fstype'	=> $this->userinfo['type'],
	          	'surplus' 	=> $b, //剩余业务
	          	'money'		=> $this->user['balance'],	//余额
	          	'status'	=> $this->user['status'],//账号状态                     
			]);
		return view();
	}
}