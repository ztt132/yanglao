<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"/Users/cesc/365web/yanglao/yanglao/public/../application/admin/view/role/edit.html";i:1593329972;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑角色</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/js/yanglao.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
    .module{padding-top: 10px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑角色</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $role['id']; ?>" name="id">
    <div class="layui-form-item">
        <label class="layui-form-label">角色名称</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="name" lay-verify="name" placeholder="请输入角色名" class="layui-input" value="<?php echo $role['name']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">权限管理</label>
        <div class="layui-input-block">
            <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): if( count($menu)==0 ) : echo "" ;else: foreach($menu as $key=>$item): ?>
                <div class="module"><?php echo $item['name']; ?></div>
                <?php if(is_array($item['sub_menu']) || $item['sub_menu'] instanceof \think\Collection || $item['sub_menu'] instanceof \think\Paginator): if( count($item['sub_menu'])==0 ) : echo "" ;else: foreach($item['sub_menu'] as $key=>$subMenu): if(in_array($subMenu['alias'],$role['menus'])): ?>
                        <input type="checkbox"  title="<?php echo $subMenu['name']; ?>" lay-filter="menus" value="<?php echo $subMenu['alias']; ?>" checked>
                    <?php else: ?>
                        <input type="checkbox"  title="<?php echo $subMenu['name']; ?>" lay-filter="menus" value="<?php echo $subMenu['alias']; ?>">
                    <?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>

<script>
    layui.use(['form'], function(){
        var form = layui.form;

        //自定义验证规则
        form.verify({
            name: function(value){
                if(value.length < 1){
                    return '请输入角色名';
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var menus = getCheckBoxValue('menus');
            $.ajax({
                url:'/admin/role/update',
                dataType:'json',
                type:'post',
                data:{
                    name:data.field.name,
                    menus:menus,
                    id:data.field.id
                },
                success:function(res){
                    if(res.code == 0){
                        parent.layer.msg(res.msg);
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.layui.table.reload('list')
                    }else{
                        $("#submit").attr('disabled',false);
                        parent.layer.alert(res.msg);
                    }
                }
            })
            return false;
        });
    });
</script>

</body>
</html>