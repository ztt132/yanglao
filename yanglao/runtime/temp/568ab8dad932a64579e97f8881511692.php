<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"/Users/cesc/365web/yanglao/yanglao/public/../application/admin/view/org/index.html";i:1593409115;}*/ ?>
<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>机构管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/font.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/xadmin.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/yanglao.css">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/xadmin.js"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
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
                <div class="layui-card-body ">
                    <form class="layui-form layui-col-space5">
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="name"  placeholder="请输入机构名" value="<?php if($name): ?><?php echo $name; endif; ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="city_name"  placeholder="请输入城市名" value="<?php if($city_name): ?><?php echo $city_name; endif; ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <button class="layui-btn"  lay-submit="" lay-filter="search"><i class="layui-icon">&#xe615;</i></button>
                        </div>
                    </form>
                </div>
                <div class="layui-card-header">
                    <button class="layui-btn" onclick="xadmin.open('添加机构','/admin/org/add',600,700)"><i class="layui-icon"></i>添加</button>
                </div>
                <table id="list" lay-filter="test" style="margin-top: 10px;"></table>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    layui.use(['table','form'], function(){
        var table = layui.table;
        var districtTable = table.render({
            elem: '#list',
            url: '/admin/org/orgList', //数据接口
            page: true, //开启分页
            method:'get',
            parseData: function(res){ //res 即为原始返回的数据
                return {
                    "code": 0, //解析接口状态
                    "msg": res.msg, //解析提示文本
                    "count": res.data.total, //解析数据长度
                    "data": res.data.data //解析数据列表
                };
            },
            cols: [[
                {field: 'id', title: 'ID', align:'center', sort: false, fixed: 'left'},
                {field: 'name', title: '机构名称', align:'center', sort: false, fixed: 'left'},
                {field: 'org_type', title: '类型', align:'center', sort: false, fixed: 'left'},
                {field: 'city_name', title: '所属城市',align:'center',  sort: false, fixed: 'left'},
                {field: 'district_name', title: '所属区域', align:'center', sort: false, fixed: 'left'},
                {field: 'company', title: '主体信息', align:'center', sort: false, fixed: 'left'},
                {field: 'price', title: '平均价格', align:'center', sort: false, fixed: 'left'},
                {field: '操作', title: '操作',align:'center',  templet: '#operation', fixed: 'left'}
            ]]
        });

        var form = layui.form;
        //监听查询
        form.on('submit(search)', function (data) {
            districtTable.reload({
                where: data.field
            });
            return false;
        });
    });

    function edit(obj) {
        var id = $(obj).attr('val');
        layui.use('layer',function(){
            var layer=layui.layer;
            layer.open({
                title:'编辑',
                type:2,
                area: ['50%', '80%'],
                content:'/admin/org/edit?id='+id
            })
        })
    }
</script>

<script type="text/html" id="operation">
    <a val="{{d.id}}" onclick="edit(this)" class="edit layui-table-link">编辑</a>
</script>
</html>