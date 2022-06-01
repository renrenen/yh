<?php

class Common{
    
    public $conf = [];
    
    public function init($url,$conf){
        $this->conf = $conf;
        $this->checkRefere();
        if($url){
            $json_url = $conf['json_api'].$url;
            $data = $this->get($json_url);
            if($data['code'] == 200 && $data['url']){
                return $this->html($data);
            }
            return $this->msg($data['msg']);
        }else{
            return $this->conf['nullurl'];
        }
        
    }
    
    public function checkRefere(){
        if(!$_SERVER['HTTP_USER_AGENT']){
            die('');
        }
        if($_SERVER['HTTP_REFERER'] && $this->conf['referer']){
            $host = parse_url($_SERVER['HTTP_REFERER'])['host'];
            if(stripos($this->conf['referer'],',')){
                $ref = explode(',',$this->conf['referer']);
                foreach ($ref as $k => $v){
                    if($host == $v){
                        break;
                    }else{
                        if($k==count($ref)-1){
                            die($this->conf['referer_tip']);
                        }
                    }
                }
            }else{
                if($host != $this->conf['referer']){
                    die($this->conf['referer_tip']);
                }
            }
        }
    }
    
    public function html($data){
        if($this->conf['type']==1){
            return '<!DOCTYPE html>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<title>'.$this->conf['title'].'</title>
	<meta name="renderer" content="webkit"/>
	<meta http-equiv="X-UA-Compatible" content="IE=11"/>
	<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" id="viewport" name="viewport">
    '.$this->cache().'
	<link rel="stylesheet" href="./Dplayer/Dplayer.min.css">
	<script type="text/javascript" src="./Dplayer/Hls.min.js"></script>
	<script type="text/javascript" src="./Dplayer/Flv.min.js"></script>
	<script type="text/javascript" src="./Dplayer/Dplayer.min.js" charset="utf-8"></script>
	<style type="text/css">
        body,html {margin: 0; padding: 0;width:100%;height:100%;overflow: hidden;background: #000;text-align:center;color: #fff;}
        .video {position: absolute;padding: 0;margin: 0;width: 100%;height: 100%;background-color: #000;color: #999;}
        #dplayer{
            height:100%;
            width:100%;
        }
    </style>
	</head>
	'.$this->fts().'
	'.$this->console().'
	<body>
	<div class="video">
	<div id="dplayer"></div>
	</div>
	<script>
	    const dp = new DPlayer({container: document.getElementById("dplayer"),screenshot: true,autoplay:true,video: {url: "'.$data['url'].'",pic: "'.$this->conf['pic'].'",},contextmenu: ['.$this->xx().'],});
	</script>
	</body>
	
	</html>';
        }elseif($this->conf['type']==2){
            header("content-type:application/json;charset=utf8");
            $arr = array(
                'code' => 200,
                'msg' => '解析成功',
                'url' => $data['url'],
                'link' => $_GET['url'],
                'from' => $_SERVER['HTTP_HOST']
                );
            return json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        }else{
            return header("location:" . $data["url"]);
        }
    }
    
    public function fts(){
        return $this->conf['debug'] ? '<script>document.oncontextmenu = function () { return false; }
document.onkeydown = function () {
var e = window.event || arguments[0];
if (e.keyCode == 123) {
    return false;
} else if ((e.ctrlKey) && (e.shiftKey) && (e.keyCode == 73)) {
    return false;
} else if ((e.shiftKey) && (e.keyCode == 121)) {
        return false;
} else if ((e.ctrlKey) && (e.keyCode == 85)) {
        return false;
}
};
</script>' : '';
    }
    
    public function tj(){
        return $this->conf['tongji'] ? '<div style="display:none"><script type="text/javascript">var cnzz_s_tag = document.createElement("script");cnzz_s_tag.type = "text/javascript";cnzz_s_tag.async = true;cnzz_s_tag.charset = "utf-8";cnzz_s_tag.src = "//'.$this->conf['tongji'].'&async=1";var root_s = document.getElementsByTagName("script")[0];root_s.parentNode.insertBefore(cnzz_s_tag, root_s);</script></div>' : '';
    }
    
    public function xx(){
        if(is_array($this->conf['xx'])){
            $str = '';
            foreach ($this->conf['xx'] as $key => $value){
                $str .= '{
                    text: "'.$key.'",
                    link: "'.$value.'",
                },';
            }
            return $str;
        }else{
            return '';
        }
        
    }
    
    public function cache(){
        return $this->conf['cache'] ? '' : '<meta http-equiv="pragma" content="no-cache"> 
    <meta http-equiv="Cache-Control" content="no-cache, must-revalidate"> 
    <meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT">';
    }
    
    public function console(){
        return '<script>console.log("\n %c 云海智能解析客户端 %c http://yhjx.shijueyy.com/ \n","color: #fadfa3; background: #030307; padding:5px 0;","background: #fadfa3; padding:5px 0;");</script>';
    }   
    
    public function msg($msg){
        if($this->conf['type']==1){
            return '<h1>'.$msg.'</h1>';
        }elseif ($this->conf['type']==2) {
            return json_encode(['code' => 404,'msg' => $msg]);
        }else{
            return $msg;
        }
        
    }
    
    public function get($url){
        $headerArray =array("Content-type:application/json;");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output,true);
        return $output;
    }
}