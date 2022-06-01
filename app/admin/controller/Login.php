<?php
namespace app\admin\controller;

use think\Request;
use app\HomeController;
use think\facade\View;
use think\facade\Db;
use think\facade\Session;

class Login extends HomeController
{
    public function index($key = '')
    {
        $login_key = config('app.check_login');
        if($login_key){
            if($key != $login_key){ 
                die('您已经开启秘钥登录，当前秘钥有误！');
            }
        }
    	view::assign([
             'name'  => '用户登录',
        ]);
        return View();    
    }

    public function login()
    {
    	if(request()->post()){
    		$username = input('post.username');
    		$password = md5(input('post.password'));
    		$result = Db::name('admin')->where(['username' => $username,'password'=>$password])->value('id');
    		if($result){
				session('admin',$result);
				$data = [
                    'code' => 200,
                    'msg'  => '登录成功',
                ];
                return json_encode($data);
			}else{
				$data = [
                    'code' => 301,
                    'msg'  => '密码错误',
                ];
                return json_encode($data);
			}
    	}
    	$token = input('get.token');
        if($token){
            $token = jiem($token);
            $token = json_decode($token,true);
            if(time()-$token[0]<=10 && $token[1]==$this->mystr){
                session('admin',$token[2]);
                return redirect('/admin');
            }else{
                return redirect('/admin');
            }
        }else{
            return redirect('/admin');
        }
    }

    public function out()
    {
    	session('admin', null);
    	session('jifei', null);
    	return redirect('/admin');
    }
    
}
