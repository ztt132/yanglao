// 根据城市重新加载区域下拉框
function loadDistrictOptions(newCityId) {
    $('#district_id').empty();
    $('#district_id').append(new Option('请选择区域', ''));
    if (!newCityId) {
        layui.form.render("select");
        return;
    }
    var districts = cityDistrict[newCityId]['districts'];
    $.each(districts, function (index, item) {
        $('#district_id').append(new Option(item.district_name, item.district_id));
    });
    // 如果存在street 还需要清空街道
    if ($('#street_id').length > 0) {
        $('#street_id').empty();
    }
    // 如果存在community 还需要清空社区
    if ($('#community_id').length > 0) {
        $('#community_id').empty();
    }
    layui.form.render("select");
}

function loadStreetOptions(newDistrictId) {
    $('#street_id').empty();
    $('#street_id').append(new Option('请选择街道', ''));
    if (!newDistrictId) {
        layui.form.render("select");
        return;
    }
    var nowCityId = $('#city_id').val();
    var streets = cityDistrict[nowCityId]['districts'][newDistrictId]['streets'];
    console.log('streets : ');
    console.log(streets);
    $.each(streets, function (index, item) {
        $('#street_id').append(new Option(item.name, item.id));
    });
    // 如果存在community 还需要清空社区
    if ($('#community_id').length > 0) {
        $('#community_id').empty();
    }
    layui.form.render("select");
}

function loadCommunityOptions(newStreetId) {
    console.log('empty');
    $('#community_id').empty();
    $('#community_id').append(new Option('请选择社区', ''));
    if (!newStreetId) {
        layui.form.render("select");
        return;
    }
    var nowCityId = $('#city_id').val();
    var nowDistrictId = $('#district_id').val();
    console.log('communitys : ');
    console.log(cityDistrict[nowCityId]['districts'][nowDistrictId]['streets'][newStreetId]['communitys']);

    var communitys = cityDistrict[nowCityId]['districts'][nowDistrictId]['streets'][newStreetId]['communitys'];
    $.each(communitys, function (index, item) {
        $('#community_id').append(new Option(item.name, item.id));
    });

    layui.form.render("select");
}

function getService() {
    var serviceHtml = "<div class='layui-input-block margin-right service_margin_top'>" +
        "<input type='text' placeholder='服务' class='layui-input service_name'>" +
        "<input type='text' placeholder='详细介绍' class='layui-input service_desc service_name_desc_margin'>" +
        "<button onclick='deleteService(this)' type='button' class='layui-btn layui-btn-primary layui-btn-sm btn_margin_left'><i class='layui-icon'></i></button>" +
        "</div>";
    return serviceHtml;
}

function validateService() {
    var result = true;
    $('#serviceParent').find("input[type=text]").each(function() {
        if (!$(this).val()) {
            result = false;
            return false;
        }
    });

    return result;
}

function executeService(data) {
    if ($('#serviceParent').find("input[type=text]").length < 1) {
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

function deleteService(obj) {
    $(obj).parent().remove();
}

var rule = {
    name: function(value){
        if(!value){
            return '请输入机构名称';
        }
    },
    city_id: function(value){
        if(!value){
            return '请选择城市';
        }
    },
    district_id: function(value){
        if(!value){
            return '请选择区域';
        }
    },
    address: function(value){
        if(!value){
            return '请输入地址';
        }
    },
    min_price: function(value){
        if(!value){
            return '请输入最低价格';
        }
        var preg = /^\d+$/;
        if (!preg.test(value)) {
            return '请输入正整数';
        }
    },
    max_price: function(value){
        if(!value){
            return '请输入最高价格';
        }
        var preg = /^\d+$/;
        if (!preg.test(value)) {
            return '请输入正整数';
        }
    },
    grade: function(value){
        if(!value){
            // return '请选择民政评级';
        }
    },
    type: function(value){
        if(!value){
            return '请选择机构类型';
        }
    },
    nature: function(value){
        if(!value){
            return '请选择机构性质';
        }
    },
    phone2: function(value){
        if(!value){
            return '请填写电话号码';
        }
    },
    // company: function(value){
        // if(!value){
            // return '请输入主体公司';
        // }
    // },
    // set_time: function(value){
    //     if(!value){
    //         return '请输入成立时间';
    //     }
    // },
    // cover_area: function(value){
    //     if(!value){
    //         return '请输入占地面积';
    //     }
    // },
    // structure_area: function(value){
    //     if(!value){
    //         return '请输入建筑面积';
    //     }
    // },
    cover_area: function(value){
        if(value){
            var preg = /^\d+$/;
            if (!preg.test(value)) {
                return '请输入正整数';
            }
        }
    },
    structure_area: function(value){
        if(value){
            var preg = /^\d+$/;
            if (!preg.test(value)) {
                return '请输入正整数';
            }
        }
    },
    bed_number: function(value){
        if(value){
            var preg = /^\d+$/;
            if (!preg.test(value)) {
                return '请输入正整数';
            }
        }
    },
    // employee_number: function(value){
    //     if(value){
    //         var preg = /^\d+$/;
    //         if (!preg.test(value)) {
    //             return '请输入正整数';
    //         }
    //     }
    // },
    target_person: function(value){
        if(!value){
            return '请输入收住对象';
        }
    },
    // service_scope: function(value){
        // if(!value){
        //     return '请选择服务范围';
        // }
    // },
}

function deleteShort()
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

function bindShort()
{
    var shortTel = $('#short_tel').val();
    if (shortTel) {
        parent.layer.msg('已绑定短号');
        return false;
    }
    var prefix = $('#prefix').val();
    var phone = $('#phone2').val();
    var name = $('#name').val();
    var city_id =$('#city_id').val();
    if (!name || !city_id) {
        parent.layer.msg('请填写名称并选择城市');
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

function submit(url,data)
{
    $.ajax({
        url:url,
        dataType:'json',
        type:'post',
        data:data,
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
}
