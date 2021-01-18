<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"/database/webroot/yanglao/public/../application/admin/view/filter/index.html";i:1593569117;}*/ ?>
<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>筛选管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/font.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/xadmin.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/yanglao.css">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/xadmin.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!--<div class="x-nav">-->
<!--<span class="layui-breadcrumb">-->
<!--<a href="">会员管理</a>-->
<!--<a href="">演示</a>-->
<!--<a><cite>导航元素</cite></a>-->
<!--</span>-->
<!--<a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" onclick="location.reload()" title="刷新">-->
<!--<i class="layui-icon layui-icon-refresh" style="line-height:30px"></i>-->
<!--</a>-->
<!--</div>-->
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card padding20">
                <table id="list"  style="margin-top: 10px;"></table>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    layui.use(['table'], function(){
        var table = layui.table;
        table.render({
            elem: '#list',
            cols: [[
                {field: 'id', title: 'ID', align:'center', sort: false, fixed: 'left'},
                {field: 'name', title: '项目', align:'center', sort: false, fixed: 'left'},
                {field: '操作', title: '操作', align:'center', templet: '#operation', fixed: 'left'},
            ]],
            data:[
                {
                    "id":1,
                    "name":"列表价格区间",
                    "action":"price"
                },
                {
                    "id":2,
                    "name":"列表筛选项",
                    "action":"listFilter"
                },
                {
                    "id":3,
                    "name":"列表快捷筛选项",
                    "action":"quickFilter"
                }
            ]

        });
    });

    function edit($this) {
        var action = $($this).attr('val');
        layui.use('layer',function(){
            var layer=layui.layer;
            layer.open({
                title:'编辑',
                type:2,
                area: ['50%', '60%'],
                content:'/admin/filter/'+action //url
            })
        })
    }


</script>

<script type="text/html" id="operation">
    <a val="{{d.action}}" onclick="edit(this)" class="edit layui-table-link">编辑</a>
</script>
</html>