var marker = null;
var map = null;
function initMap(x,y) {
    var center = new TMap.LatLng(y,x);//设置中心点坐标
    //初始化地图
    map = new TMap.Map("container", {
        center: center
    });

    //绑定点击事件
    map.on("click",function(evt){
        var lat = evt.latLng.getLat().toFixed(6);
        var lng = evt.latLng.getLng().toFixed(6);
        $('#lng').val(lng);
        $('#lat').val(lat);
        createMarker(lng,lat);
    })
}

function createMarker(lng,lat) {
    removeMarker();
    if (!marker) {
        marker = new TMap.MultiMarker({
            id: 'marker-layer',
            map: map,
            styles: {
                "marker": new TMap.MarkerStyle({
                    "width": 25,
                    "height": 35,
                    "anchor": { x: 16, y: 32 },
                    "src": 'https://mapapi.qq.com/web/lbs/javascriptGL/demo/img/markerDefault.png'
                })
            },
            geometries: [{
                "id": 'demo',
                "styleId": 'marker',
                "position": new TMap.LatLng(lat,lng),
                "properties": {
                    "title": "marker"
                }
            }]
        });
    }
}

function removeMarker() {
    if (marker) {
        marker.setMap(null);
        marker = null;
    }
}


var food_rule = {
    name: function (value) {
        if (!value) {
            return '请输入助餐点名称';
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
    street_id: function (value) {
        if (!value) {
            return '请输入街道';
        }
    },
    community_id: function (value) {
        if (!value) {
            return '请输入社区';
        }
    },
    address: function (value) {
        if (!value) {
            return '请输入地址';
        }
    },
    area: function (value) {
        if (!value) {
            return '请输入面积';
        }
    },
    opening_hours: function (value) {
        if (!value) {
            return '请输入营业时间';
        }
    },
    provide_food: function (value) {
        if (!value) {
            return '请输入提供餐饮';
        }
    },
    contacts: function (value) {
        if (!value) {
            return '请输入联系人';
        }
    },
    phone2: function (value) {
        if (!value) {
            return '请输入电话';
        }
    },

}