<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"/database/webroot/yanglao/public/../application/admin/view/qrcode/edit.html";i:1598427655;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑二维码</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/js/util.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
    .require{color: red;font-weight: bold;font-size: 14px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑二维码</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $qrcode['id']; ?>" name="id">
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>二维码名称</label>
        <div class="layui-input-block margin-right">
            <input type="text" value="<?php echo $qrcode['name']; ?>" name="name" lay-verify="name" placeholder="请输入二维码名称" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>二维码类型</label>
        <div class="layui-input-block margin-right">
            <select name="qrcode_type" lay-filter="qrcode_type" lay-verify="photo_type">
                <?php if(is_array($config) || $config instanceof \think\Collection || $config instanceof \think\Paginator): if( count($config)==0 ) : echo "" ;else: foreach($config as $key=>$item): ?>
                <option value="<?php echo $key; ?>" <?php if($key == $qrcode['qrcode_type']): ?>selected<?php endif; ?>><?php echo $item['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>对象id</label>
        <div class="layui-input-block margin-right">
            <input type="text" value="<?php echo $qrcode['obj_id']; ?>" name="obj_id" lay-verify="obj_id" placeholder="请输入对象id" class="layui-input">
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
        var $ = layui.jquery;

        //自定义验证规则
        form.verify({
            name: function(value){
                if(!value){
                    return '请输入名称';
                }
            },
            obj_id: function(value){
                if(!value){
                    return '请输入id';
                }
                if (!validateNumber(value)) {
                    return '请输入数字';
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var name = data.field.name;
            var obj_id = data.field.obj_id;
            var qrcode_type = data.field.qrcode_type;
            var id = data.field.id;
            $.ajax({
                url:'/admin/qrcode/update',
                dataType:'json',
                type:'post',
                data:{
                    obj_id:obj_id,
                    qrcode_type:qrcode_type,
                    name:name,
                    id:id
                },
                success:function(res){
                    parent.layer.msg(res.msg);
                    if(res.code == 0){
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.layui.table.reload('list')
                    }else{
                        $("#submit").attr('disabled',false);
                    }
                }
            })
            return false;
        });
    });
</script>

</body>
</html>