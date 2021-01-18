<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"/Users/cesc/365web/yanglao/yanglao/public/../application/admin/view/hx/add.html";i:1592976531;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增户型</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
    .pic_preview{max-width: 200px;margin-left: 110px;}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>新增户型</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">机构</label>
        <div class="layui-input-block margin-right">
            <select name="org_id" lay-filter="org_id" id="org_id" lay-verify="org_id">
                <option value="" selected>请选择机构</option>
                <?php if(is_array($orgs) || $orgs instanceof \think\Collection || $orgs instanceof \think\Paginator): if( count($orgs)==0 ) : echo "" ;else: foreach($orgs as $key=>$item): ?>
                <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">户型名称</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="name" lay-verify="name" placeholder="请输入户型名称" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">封面图片</label>
        <button type="button" class="layui-btn" id="upload_btn">上传</button>
        <div class="layui-upload-list">
            <input type="hidden" id="cover_pic" lay-verify="cover_pic" name="cover_pic">
            <img class="pic_preview" id="pic_preview">
            <p id="pic_text"></p>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">vr地址</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="vr" lay-verify="vr" placeholder="请输入vr地址" class="layui-input">
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
    layui.use(['form','upload'], function(){
        var form = layui.form;
        var upload = layui.upload;
        var $ = layui.jquery;

        //自定义验证规则
        form.verify({
            org_id: function(value){
                if(!value){
                    return '请选择机构';
                }
            },
            name: function(value){
                if(!value){
                    return '请输入户型名称';
                }
            },
            cover_pic: function(value){
                if(!value){
                    return '请上传封面图片';
                }
            },
            vr: function(value){
                if(!value){
                    return '请输入vr地址';
                }
            },
        });

        // 上传图片
        upload.render({
            elem: '#upload_btn',
            url: '/admin/file/upload', //改成您自己的上传接口
            done: function(res){
                parent.layer.msg(res.msg);
                if (res.code == 0) {
                    $('#pic_preview').attr('src', res.data.url);
                    $('#cover_pic').val(res.data.url);
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var cover_pic = data.field.cover_pic;
            var org_id = data.field.org_id;
            var name = data.field.name;
            var vr = data.field.vr;
            $.ajax({
                url:'/admin/hx/create',
                dataType:'json',
                type:'post',
                data:{
                    org_id:org_id,
                    vr:vr,
                    cover_pic:cover_pic,
                    name:name
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