<?php
namespace app\admin\controller;

use think\Request;
use app\AdminController;
use think\facade\View;
use think\facade\Db;
use app\user\model\Meal;

class SetMeal extends AdminController
{
    public function index()
    {
        $list = Meal::select();
        View::assign([
    		'name'	=> '套餐列表',
    		'list'  => $list,
    	]);
        return View();    
    }
    
    public function add()
    {
        if(request()->isPost()){
            $meal = new Meal;
            $_POST['intime'] = time();
            $result = $meal->save($_POST);
            if($result){
                return return_code(200,'添加成功');
            }else{
                return return_code(300,'添加失败');
            }
        }
        View::assign([
    		'name'	=> '添加套餐',
    	]);
        return View(); 
    }
    
    public function edit()
    {
        if(request()->isPost()){
            $meal = Meal::find($_POST['id']);
            $result = $meal->save($_POST);
            if($result){
                return return_code(200,'修改成功');
            }else{
                return return_code(300,'修改失败');
            }
        }
        $id = input('get.id');
        $meal = Meal::find($id);
        if(!$meal){return 'id不存在';}
        View::assign([
    		'name'	=> '套餐编辑',
    		'meal'  => $meal,
    	]);
        return View();
    }
    
    public function deletejk()
    {
        if(request()->isGet()){
            $id = input('get.id');
            $result = Meal::where('id',$id)->delete();
            header('location: ./index?re=success');
        }
    }
}