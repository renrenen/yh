<?php
namespace app\home\controller;

use app\HomeController;
use think\facade\view;
use app\admin\model\Webpz;
use pay\payFace\PayFace;
use app\admin\model\Order;
use app\user\model\User;

/**
 * note: 首页
 */
class Index extends HomeController
{

	public function index()
	{
	    $moval = Webpz::where('id',1)->field('tep,htjx')->find();
		view::assign([
			'name' => '首页',
			'jx'   => $moval['htjx'],
		]);
		return view('template/'.$moval['tep'].'/index');
	}
	
	public function notic(){
	    if(Request()->isPost()){
            $os = new PayFace();
            $res = $os->notify($_POST);
            $_POST['out_trade_no'];
            $_POST['total_amount'];
            if($res){
               $order = new Order;
               $reb    = $order->where('ordernum',$_POST['out_trade_no'])->find();
               if(!$reb->status){
                    $reb->status = 1;
                    $user = User::find($reb->user_id);
                    $user->balance = $reb->payment + $user->balance;
                    $user->save();
                    $reb->save();
                    die('success');
               }
            }
        }
	}
}