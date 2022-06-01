<?php
namespace app\admin\controller;

use app\AdminController;
use think\facade\View;
use think\Request;
use app\admin\model\Replace;
use app\admin\model\Admin;
use app\admin\model\Information;

class ResourManage extends AdminController
{
    public function mykey()
    {
        $Information = Information::find(1);
        return $Information->Cleankey;
    }
    
    public function batchImport()
    {   
        if(request()->isPost()){
            $old = input('post.old');
            $olds = explode("\r\n", $old);
            $new = input('post.naw');
            $news = explode("\r\n", $new);
            if(count($olds) != count($news)){
                return return_code(300,'格式有误！请检查');
            }
            $indata = array();
            $i = 1;
            foreach ($olds as $value){
                $data = explode('#',$value);
                $indata[$i]['name'] = $data[0];
                $indata[$i]['url'] = $data[1];
                $indata[$i]['intime'] = time();
                $indata[$i]['status'] = 1;
                $i++;
            }
            $i = 1;
            foreach ($news as $value){
                $indata[$i]['newurl'] = $value;
                $i++;
            }
            foreach ($indata as $value){
                $re = Replace::insert($value);
            }
            if($re){
                return return_code(200,'导入成功');
            }else{ 
                return return_code(300,'导入失败');
            }
        }
        View::assign([
    		'name'	=> '批量导入',
    	]);
        return View(); 
    }
    
    public function newReplace()
    {
        if(request()->isPost()){
            $Replace= new Replace;
            $Replace->name   = input('post.name');
            $Replace->url    = input('post.url');
            $Replace->newurl = input('post.newurl');
            $Replace->uid    = 1;
            $Replace->intime = time();
            $Replace->status = input('post.status');
            $result = $Replace->save();
            if($result){
                return return_code(200,'添加成功');
            }else{
                return return_code(300,'添加失败');
            }
        }
        View::assign([
    		'name'	=> '新增替换',
    	]);
        return View(); 
    }
    
    public function replacetlist()
    {
        if(request()->isPost()){
            $keyw = trim($_POST['keyw']);
            $list = Replace::where('status','<>',0)->where('name','like','%'.$keyw.'%')->order('id', 'desc')->paginate(15)->each(function($item,$key){
            if($item['uid']<20){
                $item['uid'] = Admin::where('id',$item['uid'])->value('name');
            }
            });   
        }else{
            $list = Replace::where('status','<>',0)->order('id', 'desc')->paginate(15)->each(function($item,$key){
            if($item['uid']<20){
                $item['uid'] = Admin::where('id',$item['uid'])->value('name');
            }
            });
        }
        $count = $list->total();
        $mykey = $this->mykey();
        View::assign([
    		'name'	=> '替换列表',
    		'list'  => $list,
    		'count' => $count,
    		"mykey" => $mykey
    	]);

        return View(); 
    }
    
    public function edit()
    {
        if(request()->isPost()){
            $replace = Replace::find($_POST['id']);
            $result = $replace->save($_POST);
            if($result){
                return return_code(200,'修改成功');
            }else{
                return return_code(300,'修改失败');
            }
        }
        $id = input('get.id');
        $replace = Replace::find($id);
        View::assign([
    		'name'	=> '资源编辑',
    		'replace' => $replace,
    	]);
        return View();
    }
    
    public function deletejk()
    {
        if(request()->isGet()){
            $id = input('get.id');
            $result = Replace::where('id',$id)->delete();
            header('location: ./replacetlist?re=success');
        }
    }
}
