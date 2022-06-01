<?php

namespace app;

use tools\SendMail;
use think\App;
use think\exception\ValidateException;
use think\Validate;
use think\facade\Db;
use think\facade\View;

/**
 * 控制器基础类
 */
abstract class HomeController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->mystr   = 'yunhaikeji';
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
        $web = Db::name('webpz')->where('id',1)->find();
        $this->web = $web; 
        view::assign([
                'webtitle'  => $web['name'],
                'qq'        => $web['qq'],
            ]);
        $this->msg();
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    public function msg()
    {
        if(input('get.type')){
                view::assign([
                    'pd'        => 1,
                    'msg'       => input('get.msg'),
                    'type'      => input('get.type'),    
                ]);
            }else{
                view::assign([
                    'pd'        => 0,
                    'msg'       => '',
                    'type'      => '',    
            ]);
        }
    }

    public function sendcode($toemail,$zt,$content)
    {
        $email = new SendMail();
        $result = $email->sendEmail($toemail,$zt,$content);
        if($result){
            return 200;
        }else{
            return 404;
        }
    }
}
