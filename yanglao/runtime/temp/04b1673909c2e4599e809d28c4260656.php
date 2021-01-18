<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/database/webroot/yanglao/public/../application/admin/view/estatehx/edit.html";i:1610529017;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑户型</title>
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
    .pic_list_preview{max-width: 150px;margin-left: 10px;}
    .require{color: red;font-weight: bold;font-size: 14px}
    .width100{width: 100px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑户型</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $hx['id']; ?>" name="id">
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>楼盘</label>
        <div class="layui-input-block margin-right">
            <select name="estate_id" lay-filter="estate_id" id="estate_id" lay-verify="estate_id" lay-search="">
                <option value="" selected>请选择楼盘</option>
                <?php if(is_array($estates) || $estates instanceof \think\Collection || $estates instanceof \think\Paginator): if( count($estates)==0 ) : echo "" ;else: foreach($estates as $key=>$item): ?>
                <option value="<?php echo $item['id']; ?>" <?php if($item['id'] == $hx['estate_id']): ?>selected<?php endif; ?>><?php echo $item['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>户型名称</label>
        <div class="layui-input-block margin-right">
            <input value="<?php echo $hx['name']; ?>" type="text" name="name" lay-verify="name" placeholder="请输入户型名称" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>描述</label>
        <div class="layui-input-block margin-right">
            <input value="<?php echo $hx['desc']; ?>" type="text" name="desc" lay-verify="desc" placeholder="请输入户型描述" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>图片</label>
        <div class="layui-upload">
            <button type="button" class="layui-btn" id="pics">多图片上传</button>图片限制：200KB，注：点击可删除图片
            <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;margin-left: 80px;margin-right: 80px">
                预览图：
                <div class="layui-upload-list" id="demo2">
                    <?php if(is_array($hx["pics"]) || $hx["pics"] instanceof \think\Collection || $hx["pics"] instanceof \think\Paginator): if( count($hx["pics"])==0 ) : echo "" ;else: foreach($hx["pics"] as $key=>$p): ?>
                        <img onclick="delete_pic(this)" src="<?php echo $p; ?>" class="layui-upload-img pic_list_preview">
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </blockquote>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">vr地址</label>
        <div class="layui-input-block margin-right">
            <input value="<?php echo $hx['vr']; ?>" type="text" name="vr" lay-verify="vr" placeholder="请输入vr地址" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>面积</label>
        <div class="layui-input-block margin-right">
            <input value="<?php echo $hx['area']; ?>" type="text" name="area" lay-verify="area" placeholder="请输入面积" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>户型</label>
        <div class="layui-input-block margin-right width100" style="float: left;margin: 0 30px 0 0">
            <select name="shi" lay-filter="shi" id="shi" lay-verify="shi" lay-search="">
                <option value="">请选择室</option>
                <?php if(is_array($hx_config["shi"]) || $hx_config["shi"] instanceof \think\Collection || $hx_config["shi"] instanceof \think\Paginator): if( count($hx_config["shi"])==0 ) : echo "" ;else: foreach($hx_config["shi"] as $key=>$s): ?>
                <option value="<?php echo $s; ?>" <?php if($s == $hx['shi']): ?>selected<?php endif; ?>><?php echo $s; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
        <div class="layui-input-block margin-right width100" style="float: left;margin: 0 30px 0 0">
            <select name="ting" lay-filter="ting" id="ting" lay-verify="ting" lay-search="">
                <option value="">请选择厅</option>
                <?php if(is_array($hx_config["ting"]) || $hx_config["ting"] instanceof \think\Collection || $hx_config["ting"] instanceof \think\Paginator): if( count($hx_config["ting"])==0 ) : echo "" ;else: foreach($hx_config["ting"] as $key=>$t): ?>
                <option value="<?php echo $t; ?>" <?php if($t == $hx['ting']): ?>selected<?php endif; ?>><?php echo $t; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
        <div class="layui-input-block margin-right width100" style="float: left;margin: 0 30px 0 0">
            <select name="wei" lay-filter="wei" id="wei" lay-verify="wei" lay-search="">
                <option value="">请选择卫</option>
                <?php if(is_array($hx_config["wei"]) || $hx_config["wei"] instanceof \think\Collection || $hx_config["wei"] instanceof \think\Paginator): if( count($hx_config["wei"])==0 ) : echo "" ;else: foreach($hx_config["wei"] as $key=>$w): ?>
                <option value="<?php echo $w; ?>" <?php if($w == $hx['wei']): ?>selected<?php endif; ?>><?php echo $w; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
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
    var picArr = JSON.parse('<?php echo json_encode($hx["pics"]); ?>');
    layui.use(['form','upload'], function(){
        var form = layui.form;
        var upload = layui.upload;
        var $ = layui.jquery;

        //自定义验证规则
        form.verify({
            estate_id: function(value){
                if(!value){
                    return '请选择楼盘';
                }
            },
            name: function(value){
                if(!value){
                    return '请输入户型名称';
                }
            },
            desc: function(value){
                if(!value){
                    return '请输入描述';
                }
            },
            area: function(value){
                if(!value){
                    return '请输入面积';
                }
                var preg = /^\d+$/;
                if (!preg.test(value)) {
                    return '请输入正整数';
                }
                if (value < 1) {
                    return '请输入正整数';
                }
            },
            shi: function(value){
                if(!value){
                    return '请选择室';
                }
            },
            ting: function(value){
                if(!value){
                    return '请选择厅';
                }
            },
            wei: function(value){
                if(!value){
                    return '请选择卫';
                }
            },
        });

        //多图片上传
        upload.render({
            elem: '#pics',
            url: '/admin/file/upload',
            multiple: true,
            before: function(obj){
                obj.preview(function(index, file, result){});
            },
            done: function(res){
                if (res.code == 0) {
                    var url = res.data.url;
                    // console.log(res.data.url);
                    $('#demo2').append('<img onclick="delete_pic(this)" src="'+ url +'" class="layui-upload-img pic_list_preview">')
                    //上传完毕
                    picArr.push(res.data.url)
                } else {
                    parent.layer.msg(res.msg);
                }
            }
        });


        //监听提交
        form.on('submit(submit)', function(data){
            // 处理图片
            if (picArr.length < 1) {
                parent.layer.msg('请上传图片');
                return false;
            }

            $.ajax({
                url:'/admin/estatehx/update',
                dataType:'json',
                type:'post',
                data:{
                    estate_id:data.field.estate_id,
                    vr:data.field.vr,
                    name:data.field.name,
                    desc:data.field.desc,
                    area:data.field.area,
                    shi:data.field.shi,
                    ting:data.field.ting,
                    wei:data.field.wei,
                    pics:picArr,
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

        window.delete_pic = function (obj) {
            layer.confirm('确定要删除此图片？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                console.log(picArr);
                var index = picArr.indexOf($(obj).attr('src'));
                picArr.splice(index,1)
                console.log(picArr);
                $(obj).remove();
                layer.close(layer.index);
            });
        }
    });

</script>

</body>
</html>