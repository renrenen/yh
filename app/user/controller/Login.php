<?php

namespace app\user\controller;

use think\Request;
use think\facade\View;
use think\facade\Db;
use think\facade\Session;
use app\HomeController;

/**
 * note: 用户登录
 */
class Login extends HomeController
{
	public function index()
	{
		view::assign([
			'title'		=> '用户登录',                      
		]);
		return View();
	}

	public function checkUser()
	{
		if(request()->post()){
			$username = input('post.username');
			$password = md5(input('post.password'));
			if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$username)){
				$type = 'email';
			}else{
				$type = 'username';
			}
			$result = Db::name('user')->where([$type => $username,'password' => $password])->find();
			if($result){
			    if(empty($result['status'])){
			        return return_code(400,'账号已被封禁,请联系管理员');
			     //   return redirect('/user/login.html?type=error&msg=账号已被封禁,请联系管理员');
			    }
				session('userinfo',$result['id']);
				return return_code(200,'登陆成功');
			}else{
			    return return_code(400,'用户名或密码错误');
				// return redirect('/user/login.html?type=error&msg=用户名或密码错误');
			}
		}
	}

	public function register()
	{
		view::assign([
			'title'		=> '账户注册', 
			'email' =>  $this->web['email'],
		]);
		return View();
	}

	public function reg()
	{
		if($this->web['email']==1){
			//开启注册验证
			$username = input('post.username');
			$qq = input('post.qq');
			$password = md5(input('post.password'));
			$email = input('post.email');
			if(!$email){
			    return redirect('/user/login/register?type=warning&msg=检查邮箱');
			}
			if(strlen($username)>10 | strlen($username)<4){
				return redirect('/user/login/register?type=warning&msg=用户名长度不能超过10或小于4');
			}
			if(strlen($qq)<5 | strlen($qq)>10 ) {
				return redirect('/user/login/register?type=warning&msg=请检查您的QQ');
			}
			$result = Db::name('user')->where('username',$username)->find();
			if($result){
				return redirect('/user/login/register?type=warning&msg=此用户名已被注册');
			}
			$result = Db::name('user')->where('email',$email)->find();
			if($result){
				return redirect('/user/login/register?type=warning&msg=此邮箱已被绑定');
			}
			$data = [
				'username' => $username,
				'password' => $password,
				'email'	   => $email,
				'qq'	   => $qq,
				'balance'  => 0,
				'intime'   => time(),
				'status'   => 1,
			];
			session('reguser',$data);
			//发送验证
			$code = mystr(16);
			$time = date('Y-m-d H:i:s', strtotime('+5minute'));
			$time = strtotime($time);
			$data=[
				'code' => $code,
				'email'=> $email,
				'intime'=> $time,
			];
			$id = Db::name('verification')->where('email',$email)->value('id');
			if($id){
				Db::name('verification')->where('id',$id)->update($data);
			}else{
				Db::name('verification')->insert($data);
			}

			$zt = '账户注册';
			$content = '点击此链接继续 http://'.$_SERVER['HTTP_HOST'].'/user/login/checkemail/emailcode/'.$code.' 此链接有效期为5分钟';
			$result = $this->sendcode($email,$zt,$content);
			if($result==200){
				return redirect('/user/login/?type=success&msg=验证码已发送至'.$email.'');
			}else{
				return redirect('/user/login/?type=error&msg=发送失败,请联系管理员');
			}
		}else{
			return $this->regs();
		}
	}

	public function checkemail($emailcode='')
	{
		if($emailcode){
			$check = Db::name('verification')->where(['code' => $emailcode])->find();
			$now = time();
			$last = $check['intime'];
			if($last - $now>=0){
				//验证通过
				$data = session('reguser');
				if(!$data){
				    return '<h1>请在同一个设备打开！</h1>';
				}
				$userid = Db::name('user')->insertGetId($data);
				$give = Db::name('information')->where('id',1)->value('give');
				$uid = $userid.mt_rand(1000,9999);
				$key = mystr(18);
				$data = [
					'id'      	=> $userid,
					'type'    	=> 1,
					'surplus' 	=> $give,
					'calltoday'	=> 0,
					'limitnum'  => 0,
					'user_uid'	=> $uid,
					'user_key'	=> $key,
				];
				$result = Db::name('business')->insert($data);
				if($result){
					return redirect('/user/login?type=success&msg=注册成功,请登录！');
				}
			}else{
				return redirect('/user/login/register?type=error&msg=链接已失效');
			}
		}else{
			return redirect('/user/login/register');
		}
	}

	public function regs()
	{
		if(request()->post()){
			$username = input('post.username');
			$qq = input('post.qq');
			$password = md5(input('post.password'));
			$email = $qq.'@qq.com';
			if(strlen($username)>10 | strlen($username)<4){
				return redirect('/user/login/register?type=warning&msg=用户名长度不能超过10或小于4');
			}
			if(strlen($qq)<5 | strlen($qq)>10 ) {
				return redirect('/user/login/register?type=warning&msg=请检查您的QQ');
			}
			$result = Db::name('user')->where('username',$username)->find();
			if($result){
				return redirect('/user/login/register?type=warning&msg=此用户名已被注册');
			}
			$result = Db::name('user')->where('email',$email)->find();
			if($result){
				return redirect('/user/login/register?type=warning&msg=此邮箱已被绑定');
			}
			$data = [
				'username' => $username,
				'password' => $password,
				'email'	   => $email,
				'qq'	   => $qq,
				'balance'  => 0,
				'intime'   => time(),
				'status'   => 1,
			];
			$userid = Db::name('user')->insertGetId($data);
			$give = Db::name('information')->where('id',1)->value('give');
			$uid = $userid.mt_rand(1000,9999);
			$key = mystr(18);
			$data = [
				'id'      	=> $userid,
				'type'    	=> 1,
				'surplus' 	=> $give,
				'calltoday'	=> 0,
				'limitnum'  => 0,
				'user_uid'	=> $uid,
				'user_key'	=> $key,
			];
			$result = Db::name('business')->insert($data);
			if($result){
				return redirect('/user/login?type=success&msg=注册成功');
			}
		}
	}
	/*
	* note: 找回密码
	*/
	public function recoverpw()
	{
		view::assign([
			'title'		=> '找回密码',                      
		]);
		return View();
	}

	public function repass()
	{
		if(request()->post()){
			$username = input('post.username');
			if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$username)){
					$type = 'email';
				}else{
					$type = 'username';
				}
			$result = Db::name('user')->where($type,$username)->value('email');
			if(!$result){
				return redirect('/user/login/recoverpw?type=error&msg=未找到该账户');
			}
			$code = mystr(16);
			$time = date('Y-m-d H:i:s', strtotime('+5minute'));
			$time = strtotime($time);
			$data=[
				'code' => $code,
				'email'=> $result,
				'intime'=> $time,
			];
			$id = Db::name('verification')->where('email',$result)->value('id');
			if($id){
				Db::name('verification')->where('id',$id)->update($data);
			}else{
				Db::name('verification')->insert($data);
			}
			$zt = '找回密码';
			$content = '点击此链接继续 http://'.$_SERVER['HTTP_HOST'].'/user/login/check/emailcode/'.$code.' 此链接有效期为5分钟';
			$result = sendcode($result,$zt,$content);
			if($result==200){
				return redirect('/user/login/recoverpw?type=success&msg=发送成功,注意查收！');
			}else{
				return redirect('/user/login/recoverpw?type=error&msg=发送失败,请联系管理员');
			}
		}
	}

	public function check($emailcode='')
	{
		if($emailcode){
			$check = Db::name('verification')->where(['code' => $emailcode])->find();
			$now = time();
			$last = $check['intime'];
			if($last - $now>=0){
				//验证通过
				$email = $check['email'];
				view::assign([
				'title'		=> '更换密码',
				'email'		=> $email,                      
				]);
				session('email',$email);
				return View('pass');
			}else{
				return redirect('/user/login/recoverpw?type=error&msg=链接已失效');
			}
		}else{
			return redirect('/user/login/recoverpw');
		}
	}

	public function upass()
	{
		if(request()->post()){
			$email = session('email');
			if($email){
				$password = md5(input('post.password'));
				$result = Db::name('user')->where('email',$email)->update(['password' => $password]);
				//seesion('email',null);
				return redirect('/user/login/?type=success&msg=更改成功,请登录');
			}
		}

	}
    
    public function qqlogin()
    {
        if(request()->post()){
            header('Content-Type: text/html; charset=utf-8'); //网页编码
            $type = input('post.type');
            $qrsig = input('post.qrsig');
            if($type == 'login'){
                $qrcode = array();
                $api = 'https://ssl.ptlogin2.qq.com/ptqrshow?appid=549000912&e=2&l=M&s=3&d=72&v=7&t=0.1415855' . time();
                $paras['header'] = 1;
                $ret = $this->get_curl($api,$paras);
                // var_dump($ret);
                // die();
                preg_match('/qrsig=(.*?);/', $ret, $matches);
                preg_match_all('/ (\d){3}/', $ret, $Conlen);
                $arr = explode('com;Secure;', $ret);
                $qrcode['qrsig'] = $matches[1];
                $qrcode['data'] = "data:image/png;base64,".base64_encode(trim($arr['1']));
                return json_encode($qrcode,JSON_UNESCAPED_UNICODE);
            }elseif($type== 'res'){
                $ret = array();
                $api = 'https://ssl.ptlogin2.qq.com/ptqrlogin?u1=' . urlencode('https://qzs.qzone.qq.com/') . '&ptqrtoken=' . $this->getqrtoken($qrsig) . '&ptredirect=0&h=1&t=1&g=1&from_ui=1&ptlang=2052&action=0-1-' . time() . '&js_ver=90220&js_type=1&login_sig=&pt_uistyle=40&aid=549000912&daid=5&has_onekey=1';
                $paras['cookie'] = 'qrsig=' . $qrsig . ';';
                $body = $this->get_curl($api, $paras);
                if (preg_match("/ptuiCB\('(.*?)'\)/", $body, $arr)) {
                $r = explode("','", str_replace("', '", "','", $arr[1]));
                if ($r[0] == 0) {
                    preg_match('/uin=(\d+)&/', $body, $uin);
                    $ret['data']['uin'] = $uin[1];
                    $result = Db::name('user')->where(['qq' => $uin[1] ])->value('id');
			        if($result){
			            $ret['code'] = 1;
			            $ret['msg'] = 'QQ登录成功';
				        session('userinfo',$result);
			        }else{
			            $ret['code'] = -6;
			            $ret['msg'] = '此qq未绑定';
			        }
                } elseif ($r[0] == 65) {
                    $ret['code'] = -1;
                    $ret['msg'] = '登录二维码已失效，请刷新重试！';
                } elseif ($r[0] == 66) {
                    $ret['code'] = -2;
                    $ret['msg'] = '请使用手机QQ扫码登录';
                } elseif ($r[0] == 67) {
                    $ret['code'] = -3;
                    $ret['msg'] = '正在验证二维码...';
                } else {
                    $ret['code'] = -4;
                    $ret['msg'] = '未知错误001，请刷新重试！';
                }
                } else {
                    $ret['code'] = -5;
                    $ret['msg'] = '未知错误002，请刷新重试！';
                }
                return json_encode($ret,JSON_UNESCAPED_UNICODE);
            }
        }
    }    

	public function out()
	{
		session('userinfo', null);
		return redirect('/user/login?type=success&msg=退出成功');
	}
	
	public function getqrtoken($qrsig)
	{
	    $len = strlen($qrsig);
        $hash = 0;
        for ($i = 0; $i < $len; $i++) {
            $hash += (($hash << 5) & 2147483647) + ord($qrsig[$i]) & 2147483647;
            $hash &= 2147483647;
        }
        return $hash & 2147483647;
	}
	
	public function get_curl($url,$paras=[]) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept:*/*";
        $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
        $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
        $httpheader[] = "Connection:close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        if (isset($paras['ctime'])) { // 连接超时
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $paras['ctime']);
        }
        if (isset($paras['rtime'])) { // 读取超时
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $paras['rtime']);
        }
        if (isset($paras['post'])) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paras['post']);
        }
        if (isset($paras['header'])) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        if (isset($paras['cookie'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $paras['cookie']);
        }
        if (isset($paras['refer'])) {
            if ($paras['refer'] == 1) {
                curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
            } else {
                curl_setopt($ch, CURLOPT_REFERER, $paras['refer']);
            }
        }
        if (isset($paras['ua'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $paras['ua']);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
        }
        if (isset($paras['nobody'])) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
    
     public function qqreg()
    {
        if(request()->post()){
            header('Content-Type: text/html; charset=utf-8'); //网页编码
            $type = input('post.type');
            $qrsig = input('post.qrsig');
            if($type == 'login'){
                $qrcode = array();
                $api = 'https://ssl.ptlogin2.qq.com/ptqrshow?appid=549000912&e=2&l=M&s=3&d=72&v=7&t=0.1415855' . time();
                $paras['header'] = 1;
                $ret = $this->get_curl($api,$paras);
                // var_dump($ret);
                // die();
                preg_match('/qrsig=(.*?);/', $ret, $matches);
                preg_match_all('/ (\d){3}/', $ret, $Conlen);
                $arr = explode('com;Secure;', $ret);
                $qrcode['qrsig'] = $matches[1];
                $qrcode['data'] = "data:image/png;base64,".base64_encode(trim($arr['1']));
                return json_encode($qrcode,JSON_UNESCAPED_UNICODE);
            }elseif($type== 'res'){
                $ret = array();
                $api = 'https://ssl.ptlogin2.qq.com/ptqrlogin?u1=' . urlencode('https://qzs.qzone.qq.com/') . '&ptqrtoken=' . $this->getqrtoken($qrsig) . '&ptredirect=0&h=1&t=1&g=1&from_ui=1&ptlang=2052&action=0-1-' . time() . '&js_ver=90220&js_type=1&login_sig=&pt_uistyle=40&aid=549000912&daid=5&has_onekey=1';
                $paras['cookie'] = 'qrsig=' . $qrsig . ';';
                $body = $this->get_curl($api, $paras);
                if (preg_match("/ptuiCB\('(.*?)'\)/", $body, $arr)) {
                $r = explode("','", str_replace("', '", "','", $arr[1]));
                if ($r[0] == 0) {
                    preg_match('/uin=(\d+)&/', $body, $uin);
                    $ret['data']['uin'] = $uin[1];
                    
                    
                    $result = Db::name('user')->where('qq|username',$uin[1])->value('id');
			        if($result){
			            $ret['code'] = -1;
			            $ret['msg'] = '该qq已被注册';
			        }else{
			            $data = [
				            'username' => $uin[1],
    				        'password' => '123456',
				            'email'	   => $uin[1]."@qq.com",
				            'qq'	   => $uin[1],
				            'balance'  => 0,
				            'intime'   => time(),
				            'status'   => 1,
			            ];
			            $userid = Db::name('user')->insertGetId($data);
			            $give = Db::name('information')->where('id',1)->value('give');
			            $uid = $userid.mt_rand(1000,9999);
			            $key = mystr(18);
			            $data = [
				            'id'      	=> $userid,
				            'type'    	=> 1,
				            'surplus' 	=> $give,
				            'calltoday'	=> 0,
				            'limitnum'  => 0,
				            'user_uid'	=> $uid,
				            'user_key'	=> $key,
			            ];
			            $result = Db::name('business')->insert($data);
			            if($result){
			                $ret['code'] = 1;
			                $ret['msg'] = '注册成功 默认密码123456 账号为QQ';
			            }else{
			                $ret['code'] = -1;
			                $ret['msg'] = '注册失败';
			            }
			            
			        }
			        
                } elseif ($r[0] == 65) {
                    $ret['code'] = -1;
                    $ret['msg'] = '登录二维码已失效，请刷新重试！';
                } elseif ($r[0] == 66) {
                    $ret['code'] = -2;
                    $ret['msg'] = '请使用手机QQ扫码登录';
                } elseif ($r[0] == 67) {
                    $ret['code'] = -3;
                    $ret['msg'] = '正在验证二维码...';
                } else {
                    $ret['code'] = -4;
                    $ret['msg'] = '未知错误001，请刷新重试！';
                }
                } else {
                    $ret['code'] = -5;
                    $ret['msg'] = '未知错误002，请刷新重试！';
                }
                return json_encode($ret,JSON_UNESCAPED_UNICODE);
            }
        }
    }  
}