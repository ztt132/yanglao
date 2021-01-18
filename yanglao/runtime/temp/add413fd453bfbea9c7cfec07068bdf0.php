<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"/database/webroot/yanglao/public/../application/admin/view/food/index.html";i:1603259353;}*/ ?>
<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>助餐管理</title>
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
<style>
    .layui-table-cell {
        height: auto;
        line-height: 28px;
        padding: 0 15px;
        position: relative;
        box-sizing: border-box;
    }


</style>
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
                            <input type="text" name="name"  placeholder="名称" value="<?php if($name): ?><?php echo $name; endif; ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="city_name"  placeholder="城市名" value="<?php if($city_name): ?><?php echo $city_name; endif; ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="district_name"  placeholder="区域名" value="<?php if($district_name): ?><?php echo $district_name; endif; ?>" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <button class="layui-btn"  lay-submit="" lay-filter="search"><i class="layui-icon">&#xe615;</i></button>
                        </div>
                    </form>
                </div>
                <div class="layui-card-header">
                    <button class="layui-btn" onclick="add()"><i class="layui-icon"></i>添加</button>
                </div>
                <table id="list" lay-filter="orgTableFilter" style="margin-top: 10px;"></table>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    layui.use(['table','form'], function(){
        var table = layui.table;
        var orgTable = table.render({
            elem: '#list',
            url: '/admin/food/foodList', //数据接口
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
                {field: 'name', title: '名称', align:'center', sort: false, fixed: 'left'},
                {field: 'district_name', title: '所属区域', align:'center', sort: false, fixed: 'left'},
                {field: 'pic', title: '图片', align:'center', templet:'#img', fixed: 'left'},
                {field: 'sort', title: '排序',align:'center',  templet: '#sort', fixed: 'left',event:'setSort',style:'cursor: pointer;'},
                {field: 'status', title: '状态',align:'center',  templet: '#status', fixed: 'left'},
                {field: '操作', title: '操作',align:'center',  templet: '#operation', fixed: 'left'}
            ]]
        });

        var form = layui.form;
        //监听查询
        form.on('submit(search)', function (data) {
            orgTable.reload({
                where: data.field,
                page:{
                    curr:1
                }
            });
            return false;
        });

        // 排序事件
        table.on('tool(orgTableFilter)',function (obj){
            var data = obj.data;
            if (obj.event == 'setSort') {
                var foodId = data.id;
                layer.prompt({
                    formType: 0,
                    title: '修改助餐点: ['+ data.name +'] 的排序',
                    value: data.sort
                }, function(value, index){
                    var preg = /^\d+$/;
                    if (!preg.test(value)) {
                        parent.layer.msg('请输入数字');
                        return false;
                    }
                    //这里一般是发送修改的Ajax请求
                    $.ajax({
                        url:'/admin/food/sort',
                        dataType:'json',
                        type:'post',
                        data:{
                            id:foodId,
                            sort:value
                        },
                        success:function(res){
                            parent.layer.msg(res.msg);
                            layer.close(index);
                            //同步更新表格和缓存对应的值
                            obj.update({
                                sort: value
                            });
                        }
                    })
                });
            }
        });

        form.on('submit(is_delete)',function (obj) {
            var s = $(obj.elem);
            var id = s.val();
            layer.confirm('确定要删除此助餐点？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    url:'/admin/food/delete',
                    dataType:'json',
                    type:'post',
                    data:{
                        id:id
                    },
                    success:function(res){
                        if (res.code == 0) {
                            layer.msg('删除成功', {icon: 1});
                            orgTable.reload();
                        } else {
                            layer.msg(res.msg, {icon: 1});
                        }
                    }
                })
            });
        })

        // 修改状态
        form.on('switch(status)',function (obj){
            // 目标状态
            var s = $(obj.elem);
            var id = s.attr('val');
            var status = obj.elem.checked;
            $.ajax({
                url:'/admin/food/status',
                dataType:'json',
                type:'post',
                data:{
                    id:id,
                    status:status == true ? 1 : 0
                },
                success:function(res){
                    parent.layer.msg(res.msg);
                    if (res.code != 0) {
                        orgTable.reload();
                    }
                }
            })
        });

    });

    function edit(obj) {
        var id = $(obj).attr('val');
        layui.use('layer',function(){
            var layer=layui.layer;
            layer.open({
                title:'编辑',
                type:2,
                area: ['60%', '80%'],
                content:'/admin/food/edit?id='+id
            })
        })
    }

    function add() {
        layui.use('layer',function(){
            var layer=layui.layer;
            layer.open({
                title:'新建',
                type:2,
                area: ['60%', '80%'],
                content:'/admin/food/add'
            })
        })
    }

</script>
<script type="text/html" id="img">
    <img src="{{d.pic}}">
</script>
<script type="text/html" id="status">
    {{#  if(d.status == 1){ }}
    <input val="{{d.id}}" checked type="checkbox" name="status" title="状态" lay-filter="status" value="{{d.status}}" lay-skin="switch">
    {{#  } else { }}
    <input val="{{d.id}}" type="checkbox" name="status" title="状态" lay-filter="status" value="{{d.status}}" lay-skin="switch">
    {{#  } }}
</script>
<script type="text/html" id="operation">
    <a val="{{d.id}}" onclick="edit(this)" class="edit layui-table-link">编辑</a>
    <button value="{{d.id}}" type="button" lay-filter="is_delete" lay-submit  class="layui-btn layui-btn-danger">删除</button>
</script>
</html>