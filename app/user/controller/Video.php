<?php
namespace app\user\Controller;

use app\BaseController;
use think\facade\View;
/**
 * 
 */
class video extends BaseController
{
	
	public function index()
	{
		if(input('post.url')){
			$htjx = $this->web['htjx'].input('post.url');
		}else{
			$htjx = $this->web['htjx'];
		}
		view::assign([
			'title' => '播放测试',
			'htjx'	=> $htjx,
		]);
		return view();
	}
}