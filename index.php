<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
define('UPLOAD_PATH',__DIR__ . '/uploads/');
define('HTML_PATH',str_replace('\\','/',__DIR__. '/runtime/html')); //静态缓存文件目录，HTML_PATH可任意设置，此处设为当前项目下新建的html目录


// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';


