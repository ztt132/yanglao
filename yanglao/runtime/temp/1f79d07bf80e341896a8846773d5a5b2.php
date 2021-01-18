<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"/database/webroot/yanglao/public/../application/admin/view/photo/edit.html";i:1599725052;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑相册</title>
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
    .require{color: red;font-weight: bold;font-size: 14px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑相册</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $photo['id']; ?>" name="id">
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>机构</label>
        <div class="layui-input-block margin-right">
            <select name="org_id" lay-filter="org_id" id="org_id" lay-verify="org_id" lay-search="">
                <option value="" selected>请选择机构</option>
                <?php if(is_array($orgs) || $orgs instanceof \think\Collection || $orgs instanceof \think\Paginator): if( count($orgs)==0 ) : echo "" ;else: foreach($orgs as $key=>$item): ?>
                <option value="<?php echo $item['id']; ?>" <?php if($item['id'] == $photo['org_id']): ?>selected<?php endif; ?>><?php echo $item['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>图片类型</label>
        <div class="layui-input-block margin-right">
            <select name="photo_type" lay-filter="photo_type" lay-verify="photo_type">
                <option value="" selected="">请选择图片类型</option>
                <?php if(is_array($config['photo_type']) || $config['photo_type'] instanceof \think\Collection || $config['photo_type'] instanceof \think\Paginator): if( count($config['photo_type'])==0 ) : echo "" ;else: foreach($config['photo_type'] as $key=>$item): ?>
                <option value="<?php echo $key; ?>" <?php if($key == $photo['photo_type']): ?>selected<?php endif; ?>><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>图片</label>
        <button type="button" class="layui-btn" id="upload_btn">上传</button>图片限制：200KB
        <div class="layui-upload-list">
            <input type="hidden" id="pic" value="<?php echo $photo['pic']; ?>" lay-verify="pic" name="pic">
            <img class="pic_preview" id="pic_preview" src="<?php echo $photo['pic']; ?>">
            <p id="pic_text"></p>
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
            photo_type: function(value){
                if(!value){
                    return '请选择图片类型';
                }
            },
            pic: function(value){
                if(!value){
                    return '请上传图片';
                }
            }
        });

        // 上传图片
        upload.render({
            elem: '#upload_btn',
            url: '/admin/file/upload', //改成您自己的上传接口
            done: function(res){
                parent.layer.msg(res.msg);
                if (res.code == 0) {
                    $('#pic_preview').attr('src', res.data.url);
                    $('#pic').val(res.data.url);
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var pic = data.field.pic;
            var org_id = data.field.org_id;
            var photo_type = data.field.photo_type;
            $.ajax({
                url:'/admin/photo/update',
                dataType:'json',
                type:'post',
                data:{
                    org_id:org_id,
                    photo_type:photo_type,
                    pic:pic,
                    id:data.field.id
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