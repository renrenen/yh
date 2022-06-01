<?php

namespace app\user\controller;

use think\facade\View;
use think\facade\Db;
use app\BaseController;
use think\Request;

/**
 * 
 */
class Api extends BaseController
{
	public function jx()
	{	
		View::assign([
			'title'	=> '解析接口',
			'useruid'	=> $this->userinfo['user_uid'],
			'userkey'	=> $this->userinfo['user_key'],
			'player'=> 'http://'.$_SERVER['HTTP_HOST'].'/khd/'.'?uid=' . $this->userinfo['user_uid'].'&key='.$this->userinfo['user_key'].'&url=https://v.qq.com/x/cover/mzc00200nx1hbcr/j0041h6t8nu.html',
			'jxtext'=> 'http://'.$_SERVER['HTTP_HOST'].'/home/api?type=ys&uid='.$this->userinfo['user_uid'].'&key='.$this->userinfo['user_key'].'&url=https://v.qq.com/x/cover/mzc00200nx1hbcr/j0041h6t8nu.html',
			'smjk' => 'http://'.$_SERVER['HTTP_HOST'].'/home/api?type=app&uid='.$this->userinfo['user_uid'].'&key='.$this->userinfo['user_key'].'&url=https://v.qq.com/x/cover/mzc00200nx1hbcr/j0041h6t8nu.html',
			'dsptext'=> 'http://'.$_SERVER['HTTP_HOST'].'/home/api?type=dsp&uid='.$this->userinfo['user_uid'].'&key='.$this->userinfo['user_key'].'&url=https://v.douyin.com/Jy2avQm/',
		]);
		return View();
	}

	public function submit()
	{
		if(request()->post()){
			$data=[
				'name' => input('post.name'),
				'url'  => input('post.url'),
				'newurl'=> input('post.xurl'),
				'uid'	=> $this->userinfo['user_uid'],
				'intime'=> time(),
				'status'=> 0,
			];
			Db::name('replace')->insert($data);
			return redirect('/user/api/submit?type=success&msg=提交成功');
		}else{
		View::assign([
			'title'	=> '资源提交',
		]);
		return View();
		}
	}

	public function record()
	{
		$list = Db::name('record')->where('uid',$this->userinfo['user_uid'])->order('id', 'desc')->paginate(10);
		if(input('get.page')){
			$page = input('get.page');
		}else{
			$page = 1;
		}
		View::assign([
			'title'	=> '解析记录',
			'list'	=> $list,
			'page'	=> $page,
		]);
		return View();
	}
}