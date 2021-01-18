<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 2:26 PM
 */
return [
    [
        'name' => '机构信息',
        'alias' => 'org_info',
        'sub_menu' => [
            ['name' => '机构管理','action' => '/admin/org','alias' => 'org','is_display'=>1],
            ['name' => '相册管理','action' => '/admin/photo','alias' => 'photo','is_display'=>1],
            ['name' => '户型管理','action' => '/admin/hx','alias' => 'hx','is_display'=>1],
            ['name' => '上传机构','action' => '/admin/upload','alias' => 'upload','is_display'=>0],
            ['name' => '助餐管理','action' => '/admin/food','alias' => 'food','is_display'=>1]
        ]
    ],
    [
        'name' => '养老地产',
        'alias' => 'extate_info',
        'sub_menu' => [
            ['name' => '地产管理','action' => '/admin/estate','alias' => 'estate','is_display'=>1],
            ['name' => '相册管理','action' => '/admin/estatephoto','alias' => 'estatephoto','is_display'=>1],
            ['name' => '户型管理','action' => '/admin/estatehx','alias' => 'estatehx','is_display'=>1],
            ['name' => '项目动态','action' => '/admin/estatenews','alias' => 'estatenews','is_display'=>1],
        ]
    ],
    [
        'name' => '资讯管理',
        'alias' => 'news_manage',
        'sub_menu' => [
            ['name' => '资讯管理','action' => '/admin/news','alias' => 'news','is_display'=>1]
        ]
    ],
    [
        'name' => '活动管理',
        'alias' => 'activity_manage',
        'sub_menu' => [
            ['name' => '活动管理','action' => '/admin/activity','alias' => 'activity','is_display'=>1]
        ]
    ],
    [
        'name' => '用户管理',
        'alias' => 'user_manage',
        'sub_menu' => [
            ['name' => '用户管理','action' => '/admin/userinfo','alias' => 'userinfo','is_display'=>1]
        ]
    ],
    [
        'name' => '其他管理',
        'alias' => 'other_manage',
        'sub_menu' => [
            ['name' => '城市管理','action' => '/admin/city','alias' => 'city','is_display'=>1],
            ['name' => '区域管理','action' => '/admin/district','alias' => 'district','is_display'=>1],
            ['name' => '街道管理','action' => '/admin/street','alias' => 'street','is_display'=>1],
            ['name' => '社区管理','action' => '/admin/community','alias' => 'community','is_display'=>1],
            ['name' => 'banner管理','action' => '/admin/banner','alias' => 'banner','is_display'=>1],
            ['name' => '设施设备','action' => '/admin/equipment','alias' => 'equipment','is_display'=>1],
            ['name' => '筛选管理','action' => '/admin/filter','alias' => 'filter','is_display'=>1],
            ['name' => '意见反馈','action' => '/admin/feedback','alias' => 'feedback','is_display'=>1],
            ['name' => '二维码管理','action' => '/admin/qrcode','alias' => 'qrcode','is_display'=>1]
        ]
    ],
    [
        'name' => '权限管理',
        'alias' => 'authorization_info',
        'sub_menu' => [
            ['name' => '账户管理','action' => '/admin/account','alias' => 'account','is_display'=>1],
            ['name' => '角色管理','action' => '/admin/role','alias' => 'role','is_display'=>1],
        ]
    ]
];