<?php
namespace app\admin\controller;

use app\AdminController;
use think\facade\View;
use think\Request;
use app\user\model\User;
use app\user\model\Business;

class UserManage extends AdminController
{
    public function index()
    {
        $list = User::order('id','desc')->paginate(15)->each(function($item,$key){
            $item['uid'] = Business::where('id',$item['id'])->value('user_uid');
        });
    	View::assign([
    		'name'	=> '用户列表',
    		'list'  => $list,
    	]);
        return View();    
    }
    
    public function adduser()
    {
    	View::assign([
    		'name'	=> '添加用户',
    	]);
        return View();    
    }
    
     public function edit()
    {
        if(request()->isPost()){
            if(!$_POST['username']){
                return return_code('404','参数为空');
            }
            $user = User::where('username',$_POST['username'])->find();
            $user->status = $_POST['status'];
            $user->balance = $_POST['balance']; 
            $user->save();
            $businrow = Business::find($user['id']);
            $businrow->user_uid = $_POST['user_uid'];
            $businrow->user_key = $_POST['user_key'];
            $businrow->type     = $_POST['type'];
            if(input('post.limitnum')){
                $businrow->limitnum = $_POST['limitnum'];
            }
            if($_POST['type']==2){
                $businrow->surplus  = strtotime($_POST['surplus']);
            }else{
                $businrow->surplus  = $_POST['surplus'];
            }
            $businrow->save();
            return return_code('200','保存成功');
        }
        $username = input('get.user');
        $userrow = User::where('username',$username)->find();
        $businrow = Business::where('id',$userrow['id'])->find();
        if($businrow['type']==2){
            $businrow['surplus'] = date('Y-m-d h:i:s',$businrow['surplus']);
        }
        $yhdata = [
            'username' => $userrow['username'],
            'user_uid' => $businrow['user_uid'],
            'user_key' => $businrow['user_key'],
            'surplus'  => $businrow['surplus'],
            'limitnum' => $businrow['limitnum'],
            'type'     => $businrow['type'],
            'balance'  => $userrow['balance'],
            'status'   => $userrow['status'],
            ];
    	View::assign([
    		'name'	=> '用户编辑',
    		'row'   => $yhdata,
    	]);
        return View();    
    }
    
    public function increase()
    {
        if(request()->isPost()){
            $user = new User;
            $name = input('post.name');
            $price  = input('post.price');
            $id = $user->where('username',$name)->find();
            if(!$id) return return_code(300,'未找到该用户');
            $id->balance = $id->balance+$price;
            $result = $id->save();
            if($result) return return_code(200,'加款成功'); else return return_code(300,'加款失败');
        }
    	View::assign([
    		'name'	=> '账户加款',
    	]);
        return View();    
    }
    
    public function search()
    {
        if(request()->isPost()){
            $value = trim(input('post.value'));
            if(preg_match('/@qq.com/',$value)){
                $rowdata = User::where('email',$value)->find(); 
            }else{
                $rowdata = User::where('username',$value)->find();
                if(!$rowdata){
                    $id = Business::where('user_uid',$value)->value('id');
                    if($id){
                        $rowdata = User::where('id',$id)->find();
                    }else{
                        $rowdata = '';
                    }
                }
            }
            if($rowdata){
                 $rowdata['user_uid'] = Business::where('id',$rowdata['id'])->value('user_uid');
                 $rowdata['intime'] = date('Y-m-d h:i:s',$rowdata['intime']);
                 $data = [
                     'code' => 200,
                     'msg'  => '查找成功',
                     'data' => $rowdata,
                     ];
            }else{
                 $data = [
                     'code' => 404,
                     'msg' => '未找到相关用户',
                     ]; 
            }
            return json($data);
        }
    }
    
    public function upstatus()
    {
        if(request()->isPost()){
            $id = $_POST['id'];
            $status = $_POST['e'];
            if($status=='u'){
                $status=0;
            }else{
                $status=1;
            }
            $user = User::find($id);
            $result = $user->save(['status'=>$status]);
            if($result){
                return return_code(200,'操作成功');
            }else{
               return return_code(300,'操作失败');
            }
        }
    }
    
    public function pl()
    {
        if(request()->isPost()){
            $arr = input('post.arr');
            $fs  = input('post.fs');
            if($fs == 'delete'){
                //删除
                foreach ($arr as $key=>$value){
                    if($value){
                        User::destroy($value);
                    }
                }
            }elseif($fs == 'jinyong'){
                foreach ($arr as $key=>$value){
                    if($value){
                         User::find($value)->save(['status'=>0]);
                    }
                }
            }else{
                foreach ($arr as $key=>$value){
                    if($value){
                         User::find($value)->save(['status'=>1]);
                    }
                }
            }
            return return_code(200,'操作成功');
        }
    }
}
