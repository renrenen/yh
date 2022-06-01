<?php
namespace app\user\Controller;

use think\Request;
use think\facade\View;
use app\BaseController;
use app\user\model\User;
use app\user\model\Business;

class information extends BaseController
{
	public function index()
	{	
		view::assign([
				'title'		=> '个人信息',
				'username'	=> $this->user['username'],
				'email'		=> $this->user['email'],
				'qq'		=> $this->user['qq'],
				'user_uid'	=> $this->userinfo['user_uid'],
				'user_key'	=> $this->userinfo['user_key'],
		]);
		return view();
	}

	public function upData()
	{
		if(request()->post()){
			$userModel = new User;
			$id = $this->user['id'];
			$password = input('post.password');
			$qq = input('post.qq');
			if(strlen($qq)<5 | strlen($qq)>10 ) {
					return redirect('/user/information?type=warning&msg=请检查你的QQ');
			}
			if($password!=''){
				if(strlen($password)>18 | strlen($password)<6){
					return redirect('/user/information?type=warning&msg=密码长度不能超过18个字符并且不少于6个字符');
				}
				$data = [
					'id' => $id,
					'password'   => md5($password),
				];

				$userModel->where('id',$id)->update($data);
			}else{
				$userModel->where('id',$id)->update(['qq' => $qq]);
			}
			return redirect('/user/information?type=success&msg=保存成功');
		}
	}

	public function replaceKey()
	{
			$userModel = new Business;
			$id = $this->user['id'];
			$key = $this->mystr(18);
			$userModel->where('id',$id)->update(['user_key' => $key]);
			return redirect('/user/information?type=success&msg=更换成功');
	}

	public function mystr($length)
    {
		$chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
		    'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 
		    't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 
		    'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
		    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
		    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$keys = array_rand($chars, $length); 
		$my = '';
	    for($i = 0; $i < $length; $i++)
	    {
	        // 将 $length 个数组元素连接成字符串
	        $my .= $chars[$keys[$i]];
	    }
	    return $my;
    }
}