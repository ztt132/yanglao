var estate_rule = {
    city_id: function(value){
        if(!value){
            changeTab('basic');
            return '请选择城市';
        }
    },
    district_id: function(value){
        if(!value){
            changeTab('basic');
            return '请选择区域';
        }
    },
    name: function(value){
        if(!value){
            changeTab('basic');
            return '请输入名称';
        }
    },
    price: function(value){
        if(value){
            var preg = /^\d+$/;
            if (!preg.test(value)) {
                changeTab('basic');
                return '价格请输入正整数';
            }
        }
    }
}

function deleteEstateShort()
{
    var shortTel = $('#short_tel').val();
    if (!shortTel) {
        parent.layer.msg('未绑定短号');
        return false;
    }
    var orgId = 0;
    if ($('#id').length > 0) {
        orgId = $('#id').val();
    }
    $.ajax({
        url:'/admin/Shorttel/delete',
        dataType:'json',
        type:'get',
        data:{
            short_tel:shortTel,
            org_id:orgId
        },
        success:function(res){
            if (res.code == 0) {
                parent.layer.msg('解绑成功');
                $('#short_tel').val('');
            } else {
                parent.layer.msg(res.msg);
            }
        }
    })
}

function bindEstateShort()
{
    var shortTel = $('#short_tel').val();
    if (shortTel) {
        parent.layer.msg('已绑定短号');
        return false;
    }
    var prefix = $('#prefix').val();
    var phone = $('#phone').val();
    var name = $('#name').val();
    var city_id =$('#city_id').val();
    if (!name || !city_id) {
        parent.layer.msg('请填写名称并选择城市');
        changeTab('basic');
        return false;
    }

    if (!prefix || !phone) {
        parent.layer.msg('请填写区号以及号码');
        return false;
    }
    $.ajax({
        url:'/admin/Shorttel/bind',
        dataType:'json',
        type:'get',
        data:{
            prefix:prefix,
            phone:phone,
            name:name,
            city_id:city_id
        },
        success:function(res){
            if (res.code == 0) {
                var shortTel = res.data.short_tel;
                $('#short_tel').val(shortTel);
            } else {
                parent.layer.msg(res.msg);
            }
        }
    })
}

function getServiceHtml() {
    var serviceHtml = "<div class='layui-input-block margin-right service_margin_top'>" +
        "<input type='text' placeholder='服务' class='layui-input service_name'>" +
        "<input type='text' placeholder='详细介绍' class='layui-input service_desc service_name_desc_margin'>" +
        "<button onclick='deleteService(this)' type='button' class='layui-btn layui-btn-primary layui-btn-sm btn_margin_left'><i class='layui-icon'></i></button>" +
        "</div>";
    return serviceHtml;
}

function getMedicalHtml() {
    var serviceHtml = "<div class='layui-input-block margin-right service_margin_top'>" +
        "<input type='text' placeholder='医疗' class='layui-input medical_name'>" +
        "<input type='text' placeholder='详细介绍' class='layui-input medical_desc service_name_desc_margin'>" +
        "<button onclick='deleteService(this)' type='button' class='layui-btn layui-btn-primary layui-btn-sm btn_margin_left'><i class='layui-icon'></i></button>" +
        "</div>";
    return serviceHtml;
}

function executeMedical(data) {
    if ($('#medicalParent').find("input[type=text]").length < 1) {
        data.medical = '';
        return data;
    }
    var serviceNameInput = $('#medicalParent').find(".medical_name");
    var serviceDescInput = $('#medicalParent').find(".medical_desc");
    if (serviceNameInput.length != serviceDescInput.length) {
        // 错误
    }

    var medical = [];
    serviceNameInput.each(function(i) {
        var item = {};
        item.medical_name = $(serviceNameInput[i]).val();
        item.medical_desc = $(serviceDescInput[i]).val();
        medical.push(item);
    });
    data.medical = medical;
    return data;
}

function executeEstateService(data) {
    if ($('#serviceParent').find("input[type=text]").length < 1) {
        data.service = '';
        return data;
    }
    var serviceNameInput = $('#serviceParent').find(".service_name");
    var serviceDescInput = $('#serviceParent').find(".service_desc");
    if (serviceNameInput.length != serviceDescInput.length) {
        // 错误
    }

    var service = [];
    serviceNameInput.each(function(i) {
        var item = {};
        item.service_name = $(serviceNameInput[i]).val();
        item.service_desc = $(serviceDescInput[i]).val();
        service.push(item);
    });

    data.service = service;
    return data;
}