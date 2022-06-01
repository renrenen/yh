<?php
namespace app\home\controller;

use app\admin\model\Webpz;
use app\admin\model\Record;
use app\admin\model\Replace;
use app\user\model\Business;
use app\admin\model\Information;
use app\admin\model\Order;
use app\user\model\User;
use think\facade\Db;

/**
 * note: Api模块
 */

class Api
{
    public function index()
    {
        $url = input('param.url');
        $type = input('param.type');
        $ip = input('param.ip');
        if (empty($ip)) {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        $uid = input('param.uid');
        $key = input('param.key');
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
        if ($data) {
            $uid = $data['uid'];
            $key=$data['key'];
        }
        if ($uid=='' || $key=='') {
            return '请检查您的信息';
        }
        $yw = new Business;
        $userinfo = $yw->where('user_uid', $uid)->find();
        $s = User::where('id',$userinfo['id'])->value('status');
        if(!$s){
            $data=['code' => 300,'msg'=>'您的账户已被封禁，请联系管理员'];
            return $this->res($data);
        }
        if ($userinfo['user_key']!=$key) {
            $data=['code' => 300,'msg'=>'uid和秘钥不匹配！'];
            return $this->res($data);
        }
        if ($url == '') {
            $data=["code" => 404, "msg" => "解析失败, 请输入链接!"];
            return $this->res($data);
        }
        $byurl = $url;
        if ($userinfo->type==1) {
            if ($userinfo->surplus<=0) {
                $data=['code' => 300,'msg'=>'点数已用尽请及时续费！'];
                return $this->res($data);
            }
            if ($userinfo->limitnum!=0 && $userinfo->calltoday >= $userinfo->limitnum) {
                $data=['code' => 300,'msg'=>'今日调用已经超过限制！'];
                return $this->res($data);
            }
            $userinfo->calltoday = Db::raw('calltoday+1');
        } else {
            if ($userinfo->surplus<=time()) {
                $data=['status' => 300,'msg'=>'包月已经到期,请及时续费！'];
                return $this->res($data);
            }
            if ($userinfo->limitnum!=0 && $userinfo->calltoday >= $userinfo->limitnum) {
                $data=['code' => 300,'msg'=>'今日调用已经超过限制！'];
                return $this->res($data);
            }
            $userinfo->calltoday = Db::raw('calltoday+1');
        }
        $webpz = Webpz::find(1);
        if ($webpz->th==1) {
            $newurl = $this->replace($url);
        }
        if ($newurl) {
            $data = [
                'code'  => 200,
                'msg'   => 'success',
                'type'  => 'mp4',
                'player'=> 'dplayer',
                'url'   => $newurl,
                ];
        } else {
            $px = 1; // 值为"1"则先走所设置api,值为"2"先走资源网 全局最优先替换资源库
            if ($px==1) {
               $data = $this->apijx($type,$url);
               if ($data['code']==404) {
                    $data = $this->yunsearch($byurl);
                }
            } else {
                $data = $this->yunsearch($byurl);
                if ($data['code']==404) {
                    $data = $this->apijx($type,$url);
                }
            }
        }
        if ($data['code']==200 && $userinfo->type==1) {
            $userinfo->surplus = Db::raw('surplus-1');//解析一次扣除点数
        }
        $userinfo->save();
        $re = new Record;
        $re->url = $byurl;
        $re->ip  = $ip;
        $re->uid = $uid;
        if ($data['code']==200) {
            $re->status = 1;
        } else {
            $re->status = 0;
        }
        $re->intime = time();
        $re->save();
        if ($type == 'app') {
            header("location:" . $data["url"]);
            exit();
        }
        return $this->res($data);
    }
    
    public function apijx($type,$url)
    {
        $webpz = Webpz::find(1);
        if ($type!='dsp') {
            $api = $webpz->api;
            $url = $api.$url;
            $html = $this->httpget($url, 5);
            $json = json_decode($html, true);
            if ($json['code']==200 && $json['url']!='') {
                $data = [
                    'code'  => $json['code'],
                    'msg'   => '解析成功',
                    'url'   => $json['url'],
                    'link'  => $_GET['url'],
                    'from'  => $_SERVER['HTTP_HOST'],
                    //'type'  => 'm3u8',
                    //'player'=> 'dplayer',
                ];
            } else {
            $data = [
                    'code' => 404,
                    'msg'  => '未能成功获取到该资源,'
                ];
            }
        } else {
            $api = $webpz->dspapi;
            $url = $api.$url;
            $html = $this->httpget($url, 5);
            $json = json_decode($html, true);
            if($json['code']==200 && $json['url']!=''){
                $data = [
                    'code'  => $json['code'],
                    'msg'   => '解析成功',
                    //'title' => $json['title'],
                    //'type'  => 'mp4',
                    //'player'=> 'url',
                    //'pic'   => $json['pic'],
                    'url'   => $json['url'],
                    'link' => $_GET['url'],
                    'from' => $_SERVER['HTTP_HOST'],
                ];
            }else{
            $data = [
                    'code' => 404,
                    'msg'  => '解析失败'
                ];
            }
        }
        return $data;
    }
    
    public function replace($url)
    {
        $tx="v.qq.com/x/cover/";
        $txgz="v.qq.com/x/page/";
        $txsj="m.v.qq.com/x/m/";
        $aqy="iqiyi.com";
        $yk="youku.com/v_show/";
        $mg="mgtv.com/b/";
        $tx2="qq.com/x/m/play";
        $m1905="1905.com/play/";
        $le="le.com/ptv/vplay/";
        $array=explode('://', $url);
        $tg = '';
        if (stripos($url, $tx)) {
            $url=$array[1];
            $sz=explode('qq.com/x/cover/', $url);
            $url=$sz[1];
            $pt='腾讯视频 pc';
        } elseif (stripos($url, $txsj)) {
            if (stripos($url, $tx2)) {
                $url=$array[1];
            } else {
                $url=$array[1];
                $sz=explode('qq.com/x/m/', $url);
                $url=$sz[1];
                $pt='腾讯视频 手机';
            }
        } elseif (stripos($url, $aqy)) {
            $url=$array[1];
            $sz=explode('/', $url);
            $fss=explode('html', $sz[1]);
            $url=$fss[0].'html';
            $pt='爱奇艺';
        } elseif (stripos($url, $yk)) {
            $url=$array[1];
            $sz=explode('youku.com/v_show/', $url);
            $fss=explode('html', $sz[1]);
            $url=$fss[0].'html';
            $pt='优酷';
        } elseif (stripos($url, $txgz)) {
            $url=$array[1];
            $sz=explode('qq.com/x/page/', $url);
            $url=$sz[1];
            $pt='腾讯视频';
        } elseif (stripos($url, $mg)) {
            $url=$array[1];
            $sz=explode('mgtv.com/b/', $url);
            $fss=explode('html', $sz[1]);
            $url=$fss[0].'html';
            $pt='芒果tv';
        } elseif (stripos($url, $m1905)) {
            $url=$array[1];
            $sz=explode('1905.com/play/', $url);
            $fss=explode('shtml', $sz[1]);
            $url=$fss[0].'shtml';
            $pt='1905电影网';
        } elseif (stripos($url, $le)) {
            $url=$array[1];
            $sz=explode('le.com/ptv/vplay/', $url);
            $fss=explode('html', $sz[1]);
            $url=$fss[0].'html';
            $pt='乐视';
        } else {
            $tg=1;
        }
        $newurl = Replace::where('url', 'like', '%'.$url.'%')->where(['status'=> 1])->value('newurl');
        return $newurl;
    }
    
    public function yunsearch($url)
    {
        //$url = 'http://php.cloudhai.cn/yunapi.php?url='.$url;
        $webpz = Webpz::find(1);
        $api = $webpz->zyapi;
        $url = $api.$url;
        $html = $this->httpget($url, 5);
        $json = json_decode($html, true);
        if($json['code']==200){
        $data = [
                'code'  => $json['code'],
                'msg'   => '解析成功',
                //'type'  => 'mp4',
                //'player'=> 'dplayer',
                'url'   => $json['url'],
                'link' => $_GET['url'],
                'from' => $_SERVER['HTTP_HOST']
            ];
        }else{
        $data = [
                'code' => 404,
                'msg'  => '未找到该资源,'
            ];
        }
        return $data;
    }
    
    public function res($data){
        header("content-type:application/json;charset=utf8");
        $data = json_encode($data,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo $data;exit;
    }
    
    public function returnPayment()
    {
        $data = input('get.');
        $Information = Information::find(1);
        ksort($data);
        reset($data);
        $codepay_key = $Information->zfkey;
        $sign = '';
        foreach ($data as $key => $val) {
            if ($val == '' || $key == 'sign') {
                continue;
            }
            if ($sign) {
                $sign .= '&';
            }
            $sign .= "$key=$val";
        }
        if (!input('get.pay_no') || md5($sign . $codepay_key) != input('get.sign')) {
            exit('fail');
        } else {
            $pay_id = input('get.pay_id');
            $money = input('get.money');
            $price = input('get.price');
            $param = input('get.param');
            $pay_no = input('get.pay_no');
            $pay = Order::find($param);
            if ($pay->status==1) {
                return redirect('/user/?type=success&msg=充值成功');
                die();
            }
            $pay->payment    = $money;
            if ($price == $money) {
                $pay->status = 1;
            } else {
                $pay->status = 2;
            }
            $pay->save();
            $user = User::where('username', $pay_id)->find();
            $user->balance = $money + $user->balance;
            $user->save();
            return redirect('/user/?type=success&msg=充值成功');
        }
    }
    
    public function verifyPayment()
    {
        $data = input('post.');
        $Information = Information::find(1);
        ksort($data);
        reset($data);
        $codepay_key = $Information->zfkey;
        $sign = '';
        foreach ($data as $key => $val) {
            if ($val == '' || $key == 'sign') {
                continue;
            }
            if ($sign) {
                $sign .= '&';
            }
            $sign .= "$key=$val";
        }
        if (!input('post.pay_no') || md5($sign . $codepay_key) != input('post.sign')) {
            exit('fail');
        } else {
            $pay_id = input('post.pay_id');
            $money = input('post.money');
            $price = input('post.price');
            $param = input('post.param');
            $pay_no = input('post.pay_no');
            $pay = Order::find($param);
            $pay->payment    = $money;
            if ($price == $money) {
                $pay->status = 1;
            } else {
                $pay->status = 2;
            }
            $pay->save();
            $user = User::where('username', $pay_id)->find();
            $user->balance = $money + $user->balance;
            $user->save();
            exit('success');
        }
    }
    
    function httpget($url, $timeout)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}