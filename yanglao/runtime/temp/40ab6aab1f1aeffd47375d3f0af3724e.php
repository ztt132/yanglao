<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"/database/webroot/yanglao/public/../application/admin/view/upload/index.html";i:1597911371;}*/ ?>
<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>上传机构</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/font.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/xadmin.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/yanglao.css">
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/xadmin.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="layui-fluid">
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
    <legend>指定允许上传的文件类型</legend>
</fieldset>

<button type="button" class="layui-btn" id="upload"><i class="layui-icon"></i>上传文件</button>

    <div class="layui-input-block">
        <textarea id="content" placeholder="" class="layui-textarea" readonly style="resize: none;height:400px "></textarea>
    </div>


</div>
<script>
    layui.use('upload', function(){
        var $ = layui.jquery,upload = layui.upload;

        //指定允许上传的文件类型
        upload.render({
            elem: '#upload',
            url: '/admin/upload/upload',//改成您自己的上传接口,
            accept: 'file', //普通文件
            exts:'xlsx',
            done: function(res){
                layer.msg('上传成功');
                $.each(res.data, function(k,v){
                    $('#content').val($('#content').val() + '\r\n' + k + ":" + v);
                });
            }
        });
    })


</script>
</body>
</html>