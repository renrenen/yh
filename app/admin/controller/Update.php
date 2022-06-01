<?php
namespace app\admin\controller;

use app\AdminController;
use think\facade\View;
use think\Request;
use app\common\config\Updata;
use unit\template;

class Update extends AdminController
{

    public function index()
    {
        // if(!session('jifei')) $this->check();
        // $api = 'http://'.$this->dn.'/shop';
        // $jifei = jiem(session('jifei'));
        // $jifei = explode('&',$jifei);
        $jifei= true;
    	View::assign([
    		'name'	=> '系统商店',
    		'jifei' => $jifei,
    	]);
        return View();    
    }
    
    public function upgrade()
    {
        // $api= 'http://'.$this->dn.'/edition/notice.php';
        // $json = posturl($api,$data='');
        // if($json['notice'])$json['notice']=
$aibkhtml='<li>云海计费系统 去授权版</li><br>
<li>欢迎使用云海计费系统,在您使用之前我们默认您同意系统的 条款 查看密码: <code>yunhaiweb</code></li><br>
<li>程序的使用者禁止与他人分享，分发他人，甚至盗卖程序，破解程序, 请您尊重版权，感谢！</li><br>
<li>违反者我方有权将其封禁,并打击盗版用户！ 如有发现请积极举报。</li><br>
<li>遇到问题可在群内提问,或去博客查看是否有相关教程说明，售后仅限于程序,不对其服务器等其他问题售后,需要改功程序 或 定制 请咨询！</li>';
    	View::assign([
    		'name'	  => '系统升级',
    		'edition' => ((new Updata)->edition),
    		'notice'  => $aibkhtml, //$json['notice'],
    	]);
    	
        return View();
    }
    
    public function getedition()
    {
        if(request()->isPost()){
            $api= 'http://'.$this->dn.'/edition/';
            $data = [
                'token' => session('jifei'),
                'edition' => ((new Updata)->edition),
            ];
            $json = posturl($api,$data);
            return json($json);
        }
    }
    
    public function update()
    {
        $ed = $_POST['ed'];
        $name = explode('download/',$ed);
        $file = $name[1];
        ((new Updata)->downFile($ed,'../'));
        ((new Updata)->unzip('../'.$file,'../'));
        if(file_exists('../'.$file)){
            $re = unlink('../'.$file);
            if($re){
                return return_code(200,'更新成功');
            }else{
                return return_code(500,'权限不够,请联系客服');
            }
        }else{
            return return_code(500,'更新文件未找到,请联系客服');
        }
    }
    
    public function setunit(){
        $name = input('get.name');
        $cont = ((new template)->set($name));
        $cont = file_get_contents($cont);
        view::assign([
            'name' => '插件设置',
            'cont' => $cont,
            ]);
        return View();
    }
    
    public function upunit(){
        $cont = input('post.cont');
        $name = input('post.name');
        ((new template)->up($cont,$name));
        return return_code('200','保存成功');
    }
}
