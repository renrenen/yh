<?php 

namespace app\admin\controller;
use app\admin\model\Information;

class Order extends \app\AdminController
{
    public function mykey()
    {
        $Information = Information::find(1);
        return $Information->Cleankey;
    }
    
    public function index()
    {
        $list = \app\admin\model\Order::order("id", "desc")->paginate(15)->each(function($item, $key)
        {
            $item["username"] = \app\user\model\User::where("id", $item["user_id"])->value("username");
        }

        );
        $mykey = $this->mykey();
        \think\facade\View::assign(array( "name" => "订单列表", "list" => $list, "mykey" => $mykey ));
        return View();
    }

    public function uerror()
    {
        $url = array(  );
        $a = array(  );
        $list = \app\admin\model\Record::where("status", 0)->field("url")->select()->toArray();
        foreach( $list as $v ) 
        {
            $url[] = $v["url"];
        }
        $url = array_unique($url);
        foreach( $url as $k => $v ) 
        {
            $arr = \app\admin\model\Record::where(array( "status" => 0, "url" => $v ))->find();
            $count = \app\admin\model\Record::where(array( "status" => 0, "url" => $v ))->count();
            $a[$k]["id"] = $arr["id"];
            $a[$k]["status"] = $arr["status"];
            $a[$k]["url"] = $v;
            $a[$k]["intime"] = $arr["intime"];
            $a[$k]["cs"] = $count;
        }
        usort($a, function($c, $d)
        {
            return ($c["cs"] < $d["cs"] ? 1 : -1);
        }

        );
        $num = \app\admin\model\Record::count("id");
        $success = \app\admin\model\Record::where("status", 1)->count("id");
        if( $num != 0 ) 
        {
            $b = round($success / $num * 100, 2);
        }
        else
        {
            $b = "无";
        }

        \think\facade\View::assign(array( "name" => "错误排行", "list" => $a, "b" => $b ));
        return View();
    }

    public function user()
    {
        $list = \think\facade\Db::name("business")->order("calltoday desc")->paginate(15)->each(function($item)
        {
            $item["username"] = \think\facade\Db::name("user")->where("id", $item["id"])->value("username");
            return $item;
        }

        );
        $mykey = $this->mykey();
        \think\facade\View::assign(array( "name" => "调用排行", "list" => $list,  "mykey" => $mykey ));
        return View();
    }

    public function call()
    {
        $list = \app\admin\model\Record::order("id", "desc")->paginate(15);
        $num = \app\admin\model\Record::count("id");
        $success = \app\admin\model\Record::where("status", 1)->count("id");
        if( $num != 0 ) 
        {
            $b = round($success / $num * 100, 2);
        }
        else
        {
            $b = "无";
        }
        $mykey = $this->mykey();
        \think\facade\View::assign(array( "name" => "调用记录", "list" => $list, "b" => $b,'mykey'=>$mykey ));
        return View();
    }

    public function deletejk()
    {
        if( request()->isGet() ) 
        {
            $id = input("get.id");
            $result = \app\admin\model\Order::where("id", $id)->delete();
            header("location: ./index?re=success");
        }

    }

    public function deletejk2()
    {
        if( request()->isGet() ) 
        {
            $id = input("get.id");
            $result = \app\admin\model\Record::where("id", $id)->delete();
            header("location: ./call?re=success");
        }

    }

}