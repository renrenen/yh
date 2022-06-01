<?php
namespace app\admin\controller;

use app\AdminController;
use think\facade\View;
use think\Request;
use app\admin\model\Replace;
use app\admin\model\Admin;

class SiteManage extends AdminController
{
    public function index()
    {
    	View::assign([
    		'name'	=> '站点管理',
    	]);
        return View();    
    }
    
    public function addadmin()
    {
    	View::assign([
    		'name'	=> '添加站点',
    	]);
        return View();    
    }
    
    public function examine()
    {
        $list = Replace::where('status',0)->order('id', 'desc')->paginate(15)->each(function($item,$key){
            if($item['uid']<20){
                $item['uid'] = Admin::where('id',$item['uid'])->value('name');
            }
        });
        View::assign([
    		'name'	=> '资源审核',
    		'list'  => $list
    	]);
        return View();
    }
    
    public function shenzy()
    {
        if(request()->isPost()){
            $zyid = input('post.id');
            $zy = Replace::find($zyid);
            $zy->status = 1;
            $zy->save();
            return return_code(200,'审核成功');
        }
    }
    
    public function deletezy()
    {
        if(request()->isPost()){
            $zyid = input('post.id');
            $zy = Replace::find($zyid);
            $zy->status = 1;
            $zy->save();
            return return_code(200,'删除成功');
        }
    }
}
