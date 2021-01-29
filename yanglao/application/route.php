<?php
//return [
////    '__pattern__' => [
////        'name' => '\w+',
////    ],
////    '[hello]'     => [
////        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
////        ':name' => ['index/hello', ['method' => 'post']],
////    ],
//    '' => ['/admin',['method' => 'GET']],
//    'login' => '/admin/login',
//
//];

use think\Route;
Route::get('/','/admin');

/**以下为api接口**/

/** 这三个为基础配置接口**/
Route::get('api/allCity','api/Util/getAllCity');
Route::get('api/allCity2','api/Util/getAllCity2');
Route::get('api/city','api/Util/getCityByLocation');
Route::get('api/filterConfig','api/Util/getFilterConfig');
Route::get('api/estate/filterConfig','api/Util/getEstateFilterConfig');
Route::get('api/servicePhone','api/Util/getServicePhone');
Route::get('api/getCityByPinyin','api/Util/getCityByPinyin');


//自定义路由
Route::get('api/index', 'api/index/index');
Route::get('api/get/:id', 'api/index/get');

//小程序测试demo
Route::get('api/getConfig', 'api/index/getConfig');
Route::get('api/getList', 'api/index/getList');
Route::get('api/getDetail', 'api/index/getDetail');
Route::get('api/upImg', 'api/index/upImg');
Route::get('api/save', 'api/index/save');
Route::get('api/getOpenId', 'api/index/getOpenId');
Route::post('api/getPhone', 'api/index/getPhone');



