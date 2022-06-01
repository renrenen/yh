<?php
namespace app\api\controller;
use app\user\model\Business;
use app\admin\model\Record;
use think\facade\Db;
use app\admin\model\Information;
use app\admin\model\Webpz;

class Index 
{
    public function mykey()
    {
        $Information = Information::find(1);
        return $Information->Cleankey;
    }
    
    public function index()
    {
        return '云海计费系统 轮询文件';
    }
    
    //用户调用记录清理
    public function emptyday($key='')
    {
        if($key!=$this->mykey()){
            return return_code(100,'清理密钥错误！');
        }
        //一天一清理
        $r = Business::where('id','<>',0)->update(['calltoday' => 0]);
        return redirect('/admin/Order/user?type=success&msg=清理成功');
    }
    
    //系统调用清理
    public function delre($key='')
    {
        if($key!=$this->mykey()){
            return return_code(100,'清理密钥错误！');
        }
        //可自由定义清理时间 
        Db::query('truncate table yh_record');
        return redirect('/admin/Order/call?type=success&msg=清理成功');
    }
    
    //系统订单清理
    public function emporder($key='')
    {
        if($key!=$this->mykey()){
            return return_code(100,'清理密钥错误！');
        }
        //可自由定义清理时间
        Db::query('truncate table yh_order');
        //return return_code(200,'保存成功');
        return redirect('/admin/Order/index?type=success&msg=清理成功');
    }
    
    //资源替换清理
    public function tihuan($key='')
    {
        if($key!=$this->mykey()){
            return return_code(100,'清理密钥错误！');
        }
        //可自由定义清理时间
        Db::query('truncate table yh_replace');
        return redirect('/admin/ResourManage/replacetlist?type=success&msg=清理成功');
    }
    
    //验证清理
    public function verification($key='')
    {
        if($key!=$this->mykey()){
            return return_code(100,'清理密钥错误！');
        }
        //可自由定义清理时间
        Db::query('truncate table yh_verification');
        return '清理成功';
    }
    
    //用户清理
    public function user($key='')
    {
        if($key!=$this->mykey()){
            return return_code(100,'清理密钥错误！');
        }
        //可自由定义清理时间
        Db::query('truncate table yh_user');
        Db::query('truncate table yh_business');
        return '清理成功';
    }
    
}