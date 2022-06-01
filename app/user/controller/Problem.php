<?php
namespace app\user\Controller;

use app\BaseController;
use think\facade\View;
/**
 * 
 */
class problem extends BaseController
{
	
	public function index()
	{
		view::assign([
			'title' => '常见问题',
		]);
		return view();
	}
}