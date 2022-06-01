<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------
error_reporting(E_ALL ^ E_WARNING);
return [
    // 后台登录秘钥  类似宝塔口令 留空则不开启！ 开启后登录地址为： 你的域名/admin/login/index/key/设置的秘钥
    'check_login'      => '',  
    // 应用地址
    'app_host'         => env('app.host', ''),
    //
    'DEBUG'            => true,
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => 'home',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    //'exception_tmpl'   => '../view/404.html',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => true,
];
