<?php
namespace app\user\controller;

use think\facade\View;
use app\BaseController;
use app\admin\model\Information;
use app\admin\model\Order;
use pay\payFace\PayFace;
use tools\Qrcodec;

class Pay extends BaseController
{
	
	public function index()
	{
		view::assign([
				'title'		=> '账户充值',
			]);
		return view();
	}
	
	public function payFace($price = 0){
    if(!is_numeric($price)||strpos($price,".")!==false||$price<=0){
       return json(['code' => -1,'msg' => '请填写非零非负的整数']); 
    }
	    if($price){
	        $payc = new payFace;
	        $ordernum = date('Ymd').str_pad(mt_rand(1, 99999), 5,STR_PAD_LEFT);
	        $data = $payc->creatOrder($ordernum,$price,'余额充值');
	        if($data[0]){
	             $pay             = new Order;
	             $pay->user_id    = $this->user['id'];
                 $pay->method     = 1;
                 $pay->payment    = $price;
                 $pay->ordernum   = $ordernum;
                 $pay->status     = 0;
                 $pay->intime     = time();
                 $pay->save();
                 return json(['code' => 200,'ordernum' => $data[1],'url' => $data[2]]); 
	        }else{
	             return '创建失败';
	        }
	       
	    }else{
	        return '余额充值 - 云海计费系统';
	    }
	}
	
	public function payResult($ordernum){
	    $res = Order::where('ordernum',$ordernum)->find();
	    $res->status;
	    if($res->status){
	        return json(['code' => 200,'msg' =>'支付成功']);
	    }else{
	        return json(['code' => -1,'msg' =>'未支付']);
	    }
	}
	
	public function pay()
	{
	    $dn         = $_SERVER['HTTP_HOST'];
	    $type       = input('post.type');
	    $price      = input('post.price');
	    $ordernum   = input('post.ordernum');
	    if(empty($type) || empty($price) || empty($ordernum)){
	        return redirect('/user/pay.html?type=error&msg=金额不能为空');
	    }
	    $pay           = new Order;
	    $pay->user_id    = $this->user['id'];
        $pay->method     = $type;
        $pay->payment    = $price;
        $pay->ordernum   = $ordernum;
        $pay->status     = 0;
        $pay->intime     = time();
        $pay->save();
        
	    $Information = Information::find(1);
	    $codepay_id = $Information->zfid;
        $codepay_key= $Information->zfkey;
        if(empty($codepay_id) || empty($codepay_key)){
	        return redirect('/user/pay.html?type=error&msg=暂未开启支付');
	    }
        $data = array(
            "id"            => $codepay_id,
            "pay_id"        => $this->user['username'],
            "type"          => $type,//1支付宝支付 3微信支付
            "price"         => $price,
            "param"         => $pay->id,
            "notify_url"    =>"http://$dn/home/api/verifyPayment",
            "return_url"    =>"http://$dn/home/api/returnPayment",
        ); 
        ksort($data); 
        reset($data); 
        $sign = '';
        $urls = ''; 
        foreach ($data AS $key => $val) { 
            if ($val == ''||$key == 'sign') continue;
                if ($sign != '') {
                        $sign .= "&";
                        $urls .= "&";
                }
                $sign .= "$key=$val";
                $urls .= "$key=" . urlencode($val);

            }
            $query = $urls . '&sign=' . md5($sign .$codepay_key);
            $url = "http://api5.xiuxiu888.com/creat_order/?{$query}";
            header("Location:{$url}"); //跳转到支付页面
	}
	
}