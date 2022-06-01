<?php
namespace app\user\controller;

use think\facade\View;
use think\facade\Db;
use app\user\model\User;
use app\user\model\Meal;
use app\user\model\Business;
use app\BaseController;

/**
 * 
 */
class Price extends BaseController
{
	public function index()
	{	
		$types = input('get.types');
		if(!$types){
			$types = 1;
		}
		$list = Db::name('meal')->where(['status' => 1,'type' => $types])->order('id', 'desc')->select();
		view::assign([
				'title'		=> '购买套餐',
				'list'		=> $list,
				'types'		=> $types,
		]);
		return view();
	}

	public function purChase()
	{
		$id = input('get.id');
		$datavalue = Db::name('meal')->where('id',$id)->find();
		if($this->user['balance']>=$datavalue['price']){
			$balance = $this->user['balance']-$datavalue['price'];
			$userModel = new User;
			$uid = $this->user['id'];
			$userModel->where('id',$uid)->update(['balance' => $balance]);
			$Model = new Business;
			$business = $Model->where('id',$this->user['id'])->find();
			if($datavalue['type']==1){
				//point  
				if($business['type']==1){
					if($business['surplus']==0){
						$value = $datavalue['fs'];
					}else{
						$value = $datavalue['fs']+$business['surplus'];
					}
				}else{
						$value = $datavalue['fs'];
				}
				$Model->where('id',$this->user['id'])->update(['type' => 1,'surplus' => $value,'limitnum' => 0]);
			}else{
				//month 
				$s = $datavalue['fs'];
				if($business['type']==1){
					//初次购买
					$time = date('Y-m-d H:i:s', strtotime('+'.$s.'month'));
					$time = strtotime($time);
				}elseif($business['type']==2){
					$now = date('Y-m-d',time());
					$last = date('Y-m-d',$business['surplus']);
					if($now>=$last){
						$time = date('Y-m-d H:i:s', strtotime('+'.$s.'month'));
						$time = strtotime($time);
					}else{
						$time = date('Y-m-d H:i:s', strtotime('+'.$s.'month', $business['surplus']));
						$time = strtotime($time);
					}
				}
				$Model->where('id',$this->user['id'])->update(['type' => 2,'surplus' => $time,'limitnum' => $datavalue['limtsecond']]);
			}
			return redirect('/user/price?type=success&msg=购买成功');
		}else{
			return redirect('/user/price?type=warning&msg=账户余额不足,请充值!');
		}
	}
}