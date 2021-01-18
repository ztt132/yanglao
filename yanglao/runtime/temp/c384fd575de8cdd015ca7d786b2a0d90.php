<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"/database/webroot/yanglao/public/../application/admin/view/banner/edit.html";i:1595324132;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑banner</title>
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
    <legend>编辑banner</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $banner['id']; ?>" name="id">
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>标题</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="title" lay-verify="title" placeholder="请输入标题" value="<?php echo $banner['title']; ?>" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>城市</label>
        <div class="layui-input-block margin-right">
            <select name="city_id" lay-filter="city_id">
                <?php if(is_array($citys) || $citys instanceof \think\Collection || $citys instanceof \think\Paginator): if( count($citys)==0 ) : echo "" ;else: foreach($citys as $key=>$city): ?>
                <option value="<?php echo $city['id']; ?>" <?php if($city['id'] == $banner['city_id']): ?>selected="selected"<?php endif; ?>><?php echo $city['city_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>位置</label>
        <div class="layui-input-block margin-right">
            <select name="position" lay-filter="position" id="position" lay-verify="position">
                <option value="">请选择位置</option>
                <?php if(is_array($config['position']) || $config['position'] instanceof \think\Collection || $config['position'] instanceof \think\Paginator): if( count($config['position'])==0 ) : echo "" ;else: foreach($config['position'] as $key=>$item): ?>
                <option value="<?php echo $key; ?>" <?php if($key == $banner['position']): ?>selected<?php endif; ?>><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>图片</label>
        <button type="button" class="layui-btn" id="upload_btn">上传</button>
        <div class="layui-upload-list">
            <input type="hidden" id="pic" value="<?php echo $banner['pic']; ?>" lay-verify="pic" name="pic">
            <img class="pic_preview" id="pic_preview" src="<?php echo $banner['pic']; ?>">
            <p id="pic_text"></p>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>跳转方式</label>
        <div class="layui-input-block margin-right">
            <select name="link_type" lay-filter="link_type" id="link_type" lay-verify="link_type">
                <option value="" selected>请选择跳转方式</option>
                <?php if(is_array($config['link_type']) || $config['link_type'] instanceof \think\Collection || $config['link_type'] instanceof \think\Paginator): if( count($config['link_type'])==0 ) : echo "" ;else: foreach($config['link_type'] as $key=>$item): ?>
                <option value="<?php echo $key; ?>" <?php if($key == $banner['link_type']): ?>selected<?php endif; ?>><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>地址</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="link_url" lay-verify="link_url" value="<?php echo $banner['link_url']; ?>" placeholder="H5链接/养老机构ID/资讯ID" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>截止时间</label>
        <div class="layui-input-inline">
            <input readonly type="text" name="deadline" id="deadline" lay-verify="deadline" autocomplete="on" class="layui-input" value="<?php echo $banner['deadline']; ?>" placeholder="请选择截止时间">
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
        var form = layui.form;
        var upload = layui.upload;
        var $ = layui.jquery;
        var laydate = layui.laydate;

        laydate.render({
            elem: '#deadline'
        });

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
            position: function(value){
                if(!value){
                    return '请选择位置';
                }
            },
            pic: function(value){
                if(!value){
                    return '请上传图片';
                }
            },
            link_type: function(value){
                if(!value){
                    return '请输选择跳转方式';
                }
            },
            link_url: function(value){
                if(!value){
                    return '请输入跳转链接';
                }
            },
            deadline: function(value){
                if(!value){
                    return '请设置截止时间';
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
            $.ajax({
                url:'/admin/banner/update',
                dataType:'json',
                type:'post',
                data:{
                    title:data.field.title,
                    position:data.field.position,
                    pic:data.field.pic,
                    link_type:data.field.link_type,
                    link_url:data.field.link_url,
                    id:data.field.id,
                    deadline:data.field.deadline,
                    city_id:data.field.city_id
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