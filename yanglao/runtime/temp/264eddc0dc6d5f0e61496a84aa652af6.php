<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"/Users/cesc/365web/yanglao/yanglao/public/../application/admin/view/introduce/add.html";i:1593412221;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增介绍</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/lib/wangEditor/js/wangEditor.min.js"></script>
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/wangEditor/css/wangEditor.min.css"  media="all">
    <script src="XADMIN_BASE_DIR/js/edit.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
    .pic_preview{max-width: 200px;margin-left: 110px;}
    .editor{margin-right:110px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>新增介绍</legend>
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
        <label class="layui-form-label">院长姓名</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="dean_name" lay-verify="dean_name" placeholder="请输入院长姓名" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">院长介绍</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="dean_desc" lay-verify="dean_desc" placeholder="请输入院长介绍" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">头像</label>
        <button type="button" class="layui-btn" id="upload_btn">上传</button>
        <div class="layui-upload-list">
            <input type="hidden" id="pic" lay-verify="pic" name="pic">
            <img class="pic_preview" id="pic_preview">
            <p id="pic_text"></p>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">内容</label>
        <input type="hidden" name="content" id="content" lay-filter="content" lay-verify="content">
        <div class="layui-input-block" style="z-index:0">
            <div class="editor"></div>
        </div>
    </div>


    <div class="layui-form-item margin-right">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>

<script>
    var editor;
    layui.use(['form','upload'], function(){
        // 富文本
        loadEditor();

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
            dean_name: function(value){
                if(!value){
                    return '请输入院长姓名';
                }
            },
            dean_desc: function(value){
                if(!value){
                    return '请输入院长介绍';
                }
            },
            pic: function(value){
                if(!value){
                    return '请上传头像';
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
                    $('#pic').val(res.data.url);
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var validateContent = editor.txt.text();
            if (!validateContent) {
                parent.layer.alert('请输入内容');
                return false;
            }
            $.ajax({
                url:'/admin/introduce/create',
                dataType:'json',
                type:'post',
                data:{
                    org_id:data.field.org_id,
                    dean_name:data.field.dean_name,
                    dean_desc:data.field.dean_desc,
                    pic:data.field.pic,
                    content:editor.txt.html()
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