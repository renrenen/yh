<?php

namespace app;

use think\App;
use think\exception\ValidateException;
use think\Validate;
use think\facade\Db;
use think\facade\View;
abstract class AdminController
{
    protected $request;
    protected $app;
    protected $batchValidate = false;
    protected $middleware = [];
    //public $dn = 'auth.cloudhai.cn'; //授权地址
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $adminid = session('admin');
        if (!$adminid) {
            header('location: /admin/login');
            die;
        } else {
            $admin = Db::name('admin')->where('id', $adminid)->find();
            $this->admin = $admin;
        }
        $this->initialize();
    }
    protected function initialize()
    {
        $web = Db::name('webpz')->where('id', 1)->find();
        $this->web = $web;
        view::assign(['admin' => $this->admin, 'webtitle' => $web['name'], 'qq' => $web['qq'], 'qun' => $web['qun'], 'keyword' => $web['keyword'], 'contents' => $web['contents'], 'khdurl' => $web['khdurl'], 'tep' => $web['tep'], 'lbtgg' => $web['lbtgg']]);
    }
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }
        $v->message($message);
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }
        return $v->failException(true)->check($data);
    }
    //以下是授权代码
    // public function check()
    // {
    //     $api = 'http://' . $this->dn . '/check/';
    //     $this->admin['path'] = request()->url();
    //     $this->admin['host'] = $_SERVER['HTTP_HOST'];
    //     $data = posturl($api, $this->admin);
    //     if ($data == 'error') {
    //         die('您的域名暂未授权！请联系QQ8852422');
    //     } else {
    //         if ($data == '') {
    //             die('云端连接失败！');
    //         }
    //     }
    //     session('jifei', $data);
    // }
}