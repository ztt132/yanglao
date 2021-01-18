<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"/database/webroot/yanglao/public/../application/admin/view/estatenews/add.html";i:1610521343;}*/ ?>
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
        <label class="layui-form-label"><span class="require">*</span>楼盘</label>
        <div class="layui-input-block margin-right">
            <select name="estate_id" lay-filter="estate_id" id="estate_id" lay-verify="estate_id" lay-search="">
                <option value="">请选择楼盘</option>
                <?php if(is_array($estates) || $estates instanceof \think\Collection || $estates instanceof \think\Paginator): if( count($estates)==0 ) : echo "" ;else: foreach($estates as $key=>$estate): ?>
                <option value="<?php echo $estate['id']; ?>"><?php echo $estate['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>发布时间</label>
        <div class="layui-input-inline">
            <input placeholder="yyyy-MM-dd" readonly type="text" name="publish_time" id="publish_time" lay-verify="publish_time" autocomplete="on" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>内容</label>
        <div class="layui-input-block margin-right">
            <textarea placeholder="请输入内容" class="layui-textarea" name="content" id="content" lay-filter="content" lay-verify="content"></textarea>
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
    layui.use(['form','upload','laydate'], function(){
        var form = layui.form,laydate = layui.laydate;

        laydate.render({
            elem: '#publish_time',
            trigger:'click'
        });

        //自定义验证规则
        form.verify({
            estate_id: function(value){
                if(!value){
                    return '请选择楼盘';
                }
            },
            publish_time: function(value){
                if(!value){
                    return '请输入发布时间';
                }
            },
            content: function(value){
                if(!value){
                    return '请填写发布内容';
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            $.ajax({
                url:'/admin/estatenews/create',
                dataType:'json',
                type:'post',
                data:{
                    estate_id:data.field.estate_id,
                    publish_time:data.field.publish_time,
                    content:data.field.content
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