<?php
namespace app\admin\controller;

use app\AdminController;
use think\facade\View;
use think\Request;
use app\admin\model\Webpz;
use app\admin\model\Admin;
use app\admin\model\Information;
use tools\SendMail;

class Globalsite extends AdminController
{
    public function webSite()
    {
        $moval = Webpz::where('id',1)->value('tep');
        $data = $this->getTemplate();
        $moban = '';
        foreach ($data as $value){
            if($moval == $value){
                $moban .='<option value="'.$value.'" selected>'.$value.'</option>';
            }else{
                $moban .='<option value="'.$value.'">'.$value.'</option>';
            }
        }
        $Information = Information::find(1);
    	View::assign([
    		'name'	    => '网站设置',
    		'notice'    => $Information->notice,
    		'moban'     => $moban,
    		'Cleankey' => $Information->Cleankey,
    	]);
        return View();  
    }
    
    public function apiSite()
    {
        $config = Webpz::find(1);
    	View::assign([
    		'name'	    => 'API设置',
    		'api'       => $config->api,
    		'zyapi'       => $config->zyapi,
    		'dspapi'    => $config->dspapi,
    		'htjx'      => $config->htjx,
    	]);
        return View();  
    }
    
    // public function paySite()
    // {
    //     $Information = Information::find(1);
    // 	View::assign([
    // 		'name'	=> '支付设置',
    // 		'zfid' => $Information->zfid,
    // 		'zfkey'=> $Information->zfkey,
    // 	]);
    //     return View();  
    // }
    
    public function paySite()
    {
        $config = json_decode(file_get_contents('../extend/pay/payFace/config.json'),true);
    	View::assign([
    		'name'	=> '支付设置',
    		'config' => $config,
    	]);
        return View();  
    }
    
    public function registerSite()
    {
        $config = Webpz::find(1);
        $Information = Information::find(1);
    	View::assign([
    		'name'	    => '注册设置',
    		'give'      => $Information->give,
    		'email'     => $config->email,
    		'emailname' => $config->emailname,
    		'authcode'  => $config->authcode,
    		'emailsmtp' => $config->emailsmtp,
    	]);
        return View();  
    }
    
    //网站配置
    public function configSettings()
    {
        if(request()->isPost()){
            $config = Webpz::find(1);
            $result = $config->save($_POST);
            $Information = Information::find(1);
            $Information->notice = input('post.notice');
            $Information->Cleankey = input('post.Cleankey');
            $result = $Information->save();
            if($result){
                return return_code(200,'保存成功');
            }else{
                return return_code(300,'保存失败');
            }
        }
    }
    
    //api设置
    public function apiSettings()
    {
        if(request()->isPost()){
            $config = Webpz::find(1);
            $config->api = input('post.api');
            $config->zyapi = input('post.zyapi');
            $config->dspapi = input('post.dspapi');
            $config->htjx = input('post.htjx');
            $result = $config->save();
           if($result){
                return return_code(200,'保存成功');
            }else{
                return return_code(300,'保存失败');
            }
        }
    }
    
    // //支付设置
    // public function paySettings()
    // {
    //     if(request()->isPost()){
    //         $Information = Information::find(1);
    //         $Information->zfid = input('post.zfid');
    //         $Information->zfkey = input('post.zfkey');
    //         $result = $Information->save();
    //         if($result){
    //             return return_code(200,'保存成功');
    //         }else{
    //             return return_code(300,'保存失败');
    //         }
    //     }
    // }
    
    //支付设置
    public function paySettings()
    {
        if(request()->isPost()){
            $data = input('post.');
            $result = file_put_contents('../extend/pay/payFace/config.json',json_encode($data));
            if($result){
                return return_code(200,'保存成功');
            }else{
                return return_code(300,'保存失败');
            }
        }
    }
    
    //注册设置
    public function registerSettings()
    {
        if(request()->isPost()){
            $config = Webpz::find(1);
            $config->email = input('post.email');
            $config->emailname = input('post.emailname');
            $config->authcode  = input('post.authcode');
            $config->emailsmtp = input('post.emailsmtp');
            $config->save();
            $Information = Information::find(1);
            $Information->give     = input('post.give');
            $result =$Information->save();
            if($result){
                return return_code(200,'保存成功');
            }else{
                return return_code(300,'保存失败');
            }
        }
    }
    
    public function emailText()
    {
        if(request()->isPost()){
            $config = Webpz::find(1);
            $result = sendcode($config->emailname,'邮箱测试','收到这篇邮箱证明邮箱配置成功！- 聆风计费系统');
            if($config->emailname=='' || $config->authcode==''){
                return return_code(300,'请先配置邮箱');
            }
            if($result==200){
                //成功
               return return_code(200,'发送成功');
            }else{
               return return_code(300,'发送失败');
            }
        }
    }
    
    public function getTemplate()
    {
        $filePath = '../view/home/template/';
        $handler = opendir($filePath);//当前目录中的文件夹下的文件夹
        while( ($filename = readdir($handler)) !== false ) {
            if($filename != "." && $filename != ".."){
                $dile_name[] = $filename;
            }
        }
        closedir($handler);
        return $dile_name;
    }
    
    public function updateAdmin()
    {
        $id = session('admin');
        $admin = Admin::find($id);
        if(request()->isPost()){
            if($_POST['password']==''){
                unset($_POST['password']);
            }else{
                $_POST['password'] = md5($_POST['password']);
            }
            $result = $admin->save($_POST);
            if($result==200){
               return return_code(200,'修改成功');
            }else{
               return return_code(300,'修改失败');
            }
        }
    	View::assign([
    		'name'	    => '账号设置',
    		'uname'      => $admin->name,
    		'user'      => $admin->username
    	]);
        return View();  
    }
}
