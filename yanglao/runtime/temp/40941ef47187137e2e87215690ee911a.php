<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"/Users/cesc/365web/yanglao/yanglao/public/../application/admin/view/news/edit.html";i:1593410378;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑咨询</title>
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
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑咨询</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $news['id']; ?>" name="id">
    <div class="layui-form-item">
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="title" lay-verify="title" placeholder="请输入标题" class="layui-input" value="<?php echo $news['title']; ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">城市</label>
        <div class="layui-input-block margin-right">
            <select name="city_id" lay-filter="city_id" id="city_id" lay-verify="city_id">
                <option value="">请选择城市</option>
                <?php if(is_array($citys) || $citys instanceof \think\Collection || $citys instanceof \think\Paginator): if( count($citys)==0 ) : echo "" ;else: foreach($citys as $key=>$city): ?>
                <option value="<?php echo $city['id']; ?>" <?php if($city['id'] == $news['city_id']): ?>selected="selected"<?php endif; ?>><?php echo $city['city_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">类型</label>
        <div class="layui-input-block margin-right">
            <select name="type" lay-filter="type" lay-verify="type" id="type">
                <option value="">请选择类型</option>
                <?php if(is_array($config['type']) || $config['type'] instanceof \think\Collection || $config['type'] instanceof \think\Paginator): if( count($config['type'])==0 ) : echo "" ;else: foreach($config['type'] as $key=>$item): ?>
                <option value="<?php echo $key; ?>" <?php if($key == $news['type']): ?>selected<?php endif; ?>><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">内容</label>
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
    layui.use(['form'], function(){
        // 富文本
        loadEditor();
        var content = '<?php echo $news["content"]; ?>';
        editor.txt.html(content);

        var form = layui.form;
        var $ = layui.jquery;

        //自定义验证规则
        form.verify({
            title: function(value){
                if(!value){
                    return '请输入标题';
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
                url:'/admin/news/update',
                dataType:'json',
                type:'post',
                data:{
                    title:data.field.title,
                    city_id:data.field.city_id,
                    type:data.field.type,
                    content:editor.txt.html(),
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