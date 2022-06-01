<?php
namespace app\common\config;

class Updata
{
    public $edition = '4.2';
    
    public function downFile($url,$path){
        $arr=parse_url($url);
        $fileName=basename($arr['path']);
        $file=file_get_contents($url);
        $re = file_put_contents($path.$fileName,$file);
        return $re;
    }
    
    public function unzip($filename, $path) {
        $filename = iconv("utf-8","gb2312",$filename);
        $path = iconv("utf-8","gb2312",$path);
        $resource = zip_open($filename);
        $i = 1;
        while ($dir_resource = zip_read($resource)) {
            if (zip_entry_open($resource,$dir_resource)) {
                $file_name = $path.zip_entry_name($dir_resource);
                $file_path = substr($file_name,0,strrpos($file_name, "/"));
            if(!is_dir($file_path)){
                mkdir($file_path,0777,true);
            }
            if(!is_dir($file_name)){
                $file_size = zip_entry_filesize($dir_resource);
                if($file_size<(1024*1024*30)){
                    $file_content = zip_entry_read($dir_resource,$file_size);
                    file_put_contents($file_name,$file_content);
                }else{
                    die('文件过大');
                }
            }
            zip_entry_close($dir_resource);
            }
        }
        zip_close($resource);
    }
    
}
