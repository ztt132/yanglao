<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"/database/webroot/yanglao/public/../application/admin/view/news/add.html";i:1595321842;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增资讯</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/wangEditor/css/wangEditor.min.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/lib/wangEditor/js/wangEditor.min.js"></script>
    <script src="XADMIN_BASE_DIR/js/edit.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
    .editor{margin-right:110px}
    .pic_preview{max-width: 200px;margin-left: 110px;}
    .require{color: red;font-weight: bold;font-size: 14px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>新增资讯</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>标题</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="title" lay-verify="title" placeholder="请输入标题" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>发布人</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="publisher" lay-verify="publisher" placeholder="请输入发布人" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>城市</label>
        <div class="layui-input-block margin-right">
            <select name="city_id" lay-filter="city_id" id="city_id" lay-verify="city_id">
                <option value="">请选择城市</option>
                <?php if(is_array($citys) || $citys instanceof \think\Collection || $citys instanceof \think\Paginator): if( count($citys)==0 ) : echo "" ;else: foreach($citys as $key=>$city): ?>
                <option value="<?php echo $city['id']; ?>"><?php echo $city['city_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>类型</label>
        <div class="layui-input-block margin-right">
            <select name="type" lay-filter="type" lay-verify="type" id="type">
                <option value="">请选择类型</option>
                <?php if(is_array($config['type']) || $config['type'] instanceof \think\Collection || $config['type'] instanceof \think\Paginator): if( count($config['type'])==0 ) : echo "" ;else: foreach($config['type'] as $key=>$item): ?>
                <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>图片</label>
        <button type="button" class="layui-btn" id="upload_btn">上传</button>
        <div class="layui-upload-list">
            <input type="hidden" id="pic" lay-verify="pic" name="pic">
            <img class="pic_preview" id="pic_preview">
            <p id="pic_text"></p>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>内容</label>
        <input type="hidden" name="contnet" id="content" lay-filter="content" lay-verify="content">
        <div class="layui-input-block" style="z-index:0">
            <div class="editor"></div>
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
    var editor;
    layui.use(['form','upload'], function(){
        // 富文本
        loadEditor();

        var form = layui.form;
        var upload = layui.upload;

        //自定义验证规则
        form.verify({
            title: function(value){
                if(!value){
                    return '请输入标题';
                }
            },
            publisher: function(value){
                if(!value){
                    return '请输入发布人';
                }
            },
            city_id: function(value){
                if(!value){
                    return '请选择城市';
                }
            },
            type: function(value){
                if(!value){
                    return '请选择类型';
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
            var validateContent = editor.txt.text();
            if (!validateContent) {
                parent.layer.alert('请输入内容');
                return false;
            }
            console.log(editor.txt.text());
            $.ajax({
                url:'/admin/news/create',
                dataType:'json',
                type:'post',
                data:{
                    title:data.field.title,
                    city_id:data.field.city_id,
                    type:data.field.type,
                    content:editor.txt.html(),
                    publisher:data.field.publisher,
                    pic:data.field.pic
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