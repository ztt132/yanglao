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

// [ 应用入口文件 ]

// 定义应用目录
header('Access-Control-Allow-Origin:*');
define('APP_PATH', __DIR__ . '/../application/');
require __DIR__ . '/../../vendor/autoload.php';
// 加载框架引导文件
require __DIR__ . '/../../vendor/topthink/framework/start.php';
