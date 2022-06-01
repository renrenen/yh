<?php
namespace app\admin\controller;

use app\AdminController;
use think\facade\View;
use app\user\model\User;
use app\admin\model\Order;
use app\admin\model\Information;
use app\admin\model\Record;
use app\user\model\Business;

class Index extends AdminController
{
    public function index()
    {
        
        $usernum = User::count();

        $month = date('Y-m');
        $month = strtotime($month);
        
        $lastmonth = date('Y-m',strtotime("-1 month"));
        $lastmonth = strtotime($lastmonth);
        
        $payment = Order::where('status',1)->sum('payment');
        
        $lastmonth = Order::where('intime','<=',$month)->where('intime','>=',$lastmonth)->where('status',1)->sum('payment');
        $month = Order::where('intime','>=',$month)->where('status',1)->sum('payment');
        
        $zd = Information::count();
        
        $time = date('Y-m-d');
        $time = strtotime($time);
        
        $num = Record::where('intime','>=',$time)->count();
        if($lastmonth!=0){
            $c = $month-$lastmonth;
            $b = round($c/$lastmonth*100,2);
        }else{
            $b = round($month*100,2);
        }
        $bdnum = Business::where('type',1)->count();
        $bynum = Business::where('type',2)->count();
    	View::assign([
    		'name'	    => '仪表盘',
    		'usernum'   => $usernum,
    		'payment'   => $payment,
    		'month'     => $month,
    		'lastmonth' => $lastmonth,
    		'b'         => $b,
    		'zd'        => $zd,
    		'num'       => $num,
    		'bynum'     => $bynum,
    		'bdnum'     => $bdnum,
    	]);
        
        return View();    
    }
    
    public function tjnum()
    {
        if(date('H')<=12) $time= [1,2,3,4,5,6,7,8,9,10,11,12]; else $time = [13,14,15,16,17,18,19,20,21,22,23,24];
        foreach ($time as $value){
            $cx = date('Y-m-d '.$value.':00:00');
            $cx = strtotime($cx);
            $next = $value+1;
            $dx = date('Y-m-d '.$next.':00:00');
            $dx = strtotime($dx);
            $num = Record::where('intime','>=',$cx)->where('intime','<=',$dx)->count('id');
            $data['today'][$value] = $num;
        }
        foreach ($time as $value){
            $cx = date('Y-m-d '.$value.':00:00',strtotime("-1 day"));
            $cx = strtotime($cx);
            $next = $value+1;
            $dx = date('Y-m-d '.$next.':00:00',strtotime("-1 day"));
            $dx = strtotime($dx);
            $num = Record::where('intime','>=',$cx)->where('intime','<=',$dx)->count('id');
            $data['lastday'][$value] = $num;
        }
        $data['time']= $time;
        $data['bd'] = Business::where('type',1)->count();
        $data['by'] = Business::where('type',2)->count();
        $sum = $data['bd']+$data['by'];
        $data['byb']= round($data['by']/$sum*100,2);
        $data['bdb']= round($data['bd']/$sum*100,2);
        $data['ph'] = Business::order('calltoday desc')->limit(5)->column('calltoday','user_uid');

        return json($data);
    }
}
