<?php

return[
    // 是否开启错误提示
    'debug'=>true,
    // URL伪静态后缀
    'url_html_suffix'=>".html",
    //输出格式 html,json,xml
    'response_type'=>"html",
    //是否显示入口文件
    'path_info_index'=>false,
    // 强制pathInfo
    'path_info_only'=>false,
    //安全监测(如果触犯安全规则IP会被暂时拉黑，此功能需开启redis)
    'safety' => true,
    //恶意操作触碰频率
    'safety_frequency' => 3,
    //密码盐
    'admin_salt' => '6291b6%4b2f#82b1&c3',
    //路由
    'route'=>[
        'default_app'=> 'index', //默认应用
        'default_class' => 'Index',//默认控制器
        'default_fun' => '',//默认方法(默认为index)
        'null_route' => [
            'type' => true,
            'app' => 'index',
            'controller' => 'Index',
            'action' => 'longUrl'
        ]//路由不存在则转发到
    ],
    //Session配置
    'session'=>[
        'state' => true,//是否开启session
        'expire'=>60*24*7,// 过期时间
        'secure'=>false,//安全选项
        'name'=>"oreo_",//session名称
    ],
    //Redis配置
    'redis'=>[
        'host'=>"127.0.0.1",
        'port'=>"6379",
        'password'=>"",
    ],
];
