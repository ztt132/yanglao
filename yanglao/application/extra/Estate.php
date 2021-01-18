<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/11
 * Time: 9:17 AM
 */
return [
    // page中为新增编辑界面中的表单元素。地产新增编辑中表单元素太多。方便管理
    'form' => [
        // 基本信息
        'basic' => [
            ['name' => '名称','require' => 1,'key' => 'name','type' => 'text'],
            ['name' => '项目类型','require' => 0,'key' => 'type','type' => 'select'],
            ['name' => '项目性质','require' => 0,'key' => 'nature','type' => 'select'],
            ['name' => '物业类型','require' => 0,'key' => 'channel','type' => 'text'],
            ['name' => '项目地址','require' => 0,'key' => 'address','type' => 'text'],
            ['name' => '项目标签','require' => 0,'key' => 'tag','type' => 'text'],
            ['name' => '运营模式','require' => 0,'key' => 'operation','type' => 'text'],
            ['name' => '开发商','require' => 0,'key' => 'developers','type' => 'text'],
            ['name' => '物业公司','require' => 0,'key' => 'channel_company','type' => 'text'],
            ['name' => '容积率','require' => 0,'key' => 'rjl','type' => 'text'],
            ['name' => '绿化率','require' => 0,'key' => 'lvhua','type' => 'text'],
            ['name' => '总户数','require' => 0,'key' => 'house_num','type' => 'text'],
            ['name' => '占地面积','require' => 0,'key' => 'cover_area','type' => 'text'],
            ['name' => '建筑面积','require' => 0,'key' => 'structure_area','type' => 'text'],
            ['name' => '开盘时间','require' => 0,'key' => 'kp_time','type' => 'text'],
            ['name' => '交付时间','require' => 0,'key' => 'jf_time','type' => 'text'],
            ['name' => '装修情况','require' => 0,'key' => 'zhuangxiu','type' => 'text'],
            ['name' => '价格','require' => 0,'key' => 'price','type' => 'text'],
        ],
        // 运营信息
        'operation' => [
            ['name' => '运营单位','require' => 0,'key' => 'operation_unit','type' => 'text'],
            ['name' => '床位数量','require' => 0,'key' => 'bed_num','type' => 'text'],
            ['name' => '医护人数','require' => 0,'key' => 'employee_num','type' => 'text'],
            ['name' => '收住对象','require' => 0,'key' => 'revive_obj','type' => 'text'],
            ['name' => '服务范围','require' => 0,'key' => 'service_scope','type' => 'text'],
//            ['name' => '费用类型','require' => 0,'key' => 'cost_type','type' => 'textarea']
        ],
        // 配套信息
        'assort' => [
            ['name' => '高速','require' => 0,'key' => 'gaosu','type' => 'text'],
            ['name' => '医院','require' => 0,'key' => 'hospital','type' => 'text'],
            ['name' => '公交','require' => 0,'key' => 'gongjiao','type' => 'text'],
            ['name' => '地铁','require' => 0,'key' => 'subway','type' => 'text'],
            ['name' => '商业','require' => 0,'key' => 'business','type' => 'text'],
            ['name' => '风景','require' => 0,'key' => 'scenery','type' => 'text'],
        ]
    ],
    // 枚举值
    // 表单中下拉选择、复选框、单选
    // 小程序筛选中枚举
    'enum' => [
        'type' => ['养老社区','养老公寓','大型CCRC综合社区'],//类型
        'nature' => ['公办民营','公建民营','民办民营','社会团体','其它'],//性质
        'hx' => ['一室','二室','三室','四室','五室','五室以上'],
        'area' => ['60以下','60-80','80-100','100-120','120-140','140-160','160-200','200-300','300以上'],
        'vr' => ['无','有'],
        'price' => [
            'nj' => ['10000以下','10000-15000','15000-20000','20000-25000','25000-30000','30000-35000','35000-40000','40000-45000','45000以上']
        ]
    ],
    // 小程序筛选项的枚举
    'filter_page' => [
        ['key'  => 'hx', 'option' => '户型'],
        ['key'  => 'area', 'option' => '面积'],
        ['key'  => 'nature', 'option' => '项目性质'],
        ['key'  => 'vr', 'option' => '有无vr'],
    ]




];