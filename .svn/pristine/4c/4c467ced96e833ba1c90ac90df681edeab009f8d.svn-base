<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/3
 * Time: 9:35
 */

/*return [

    // 行为扩展定义
    'tags'  => [
    //开启静态缓存
    'html_cache_on' => true,
    //全局静态缓存有效期
    'html_cache_time' => 2,
    //设置静态缓存后缀
    'html_file_suffix' => '.html',

    //定义静态缓存规则
    'html_cache_rules' => [
        //  '静态地址'=>['静态规则','有效期','附加规则'],
        //3.Static控制器的所有操作
        'Static:'=>array('{:module}/{:controller}/{:action}',50),//第一个参数是构造的字符串，后面是缓存50秒
        //1.首页静态缓存 30秒
        'Index:Index'=>array('{:module}_{:controller}_{:action}',2),  // 首页静态缓存 3秒钟
      ],
    ]
];*/

return [
    'HTML_CACHE_ON'     =>    FALSE, // 开启静态缓存
    'HTML_CACHE_TIME'   =>    1200,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'  =>    '.html', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES'  => array(   //静态规则
        //'index:'    => array('index_{:action}','1200'),
        //'auction:index'  => array('auction_index','3600'),
        'Index:Index'=>array('{:module}_{:controller}_{:action}',1),  // 首页静态缓存 3秒钟
        //'*'=>array('{$_SERVER.REQUEST_URI|md5}',10),
    ),

    'home_lang_list' =>['cn'=>'中文','en'=>"英文",'tc'=>"繁体"],
    'template'               => [
        // 模版引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模版路径
        'view_path'    => './template/',
        // 模版后缀
        'view_suffix'  => 'html',
    ],

];