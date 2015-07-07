/* 
* @Author: anchen
* @Date:   2015-07-02 14:29:33
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-07 10:00:15
*/

(function(){

    var type = utils.getSearchString("type");

    if(type && type === 'edit'){
        var data = JSON.parse(sessionStorage.getItem('edit-shop-data'));
		lib.ajatCount--;
        lib.ajat('#domid=form&tempid=form-t').template(data);     
    }

    if(type && type === 'add'){
		lib.ajatCount--;
        lib.ajat('#domid=form&tempid=form-t').template({});    
    }
    
    $("#addCoordinate").on('click',function(){
        $("#pop-wrapper").show();
    })

    $(".pop-close").on('click',function(){
        $("#pop-wrapper").hide();
    });

    lib.Form.prototype.save = function(data){
        if(type && type === 'edit'){
            var shopData = JSON.parse(sessionStorage.getItem('eidt-shop-data'));
            shopData = $.extend({},shopData,data);
            sessionStorage.setItem('add-shop-data',JSON.stringify(shopData));   
        }
        if(type && type === 'add'){
            var shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
            shopData = $.extend({},shopData,data);
            sessionStorage.setItem('add-shop-data',JSON.stringify(shopData));            
        }
        location.href = "bank.html?type="+type;
    }        

})();


function renderMap (){
    $("#addCoordinate").on('click',function(){
        var address = $("#address");
        var geoCoor = $("#pop-geo-coor");
        address.val() && geoCoor.text(address.val());
        var addrPonit = {};
        //在指定的容器内创建地图实例  
        var map = new BMap.Map("map-wrapper");
        //默认经纬度point
        var point = new BMap.Point("113.941893","22.535644");
        //初始化地图位置（未进行初始化的地图将不能进行任何操作）
        map.centerAndZoom(point,11);
        //将平移缩放控件添加到地图上
        map.addControl(new BMap.NavigationControl());
        //启用滚轮放大缩小功能
        map.enableScrollWheelZoom(true);
        //此处是一个补丁，由于未知的原因直接new Marker对象添加的标注
        //标注的图标很小，所以这里直接用自己的图标
        var markerIcon = new BMap.Icon("../../images/marker.png", new BMap.Size(39,25),{
            anchor : new BMap.Size(10,25) 
        });
        //根据point获取address实例
        var geocoder = new BMap.Geocoder();

        function drawMarker(point){
            var marker = new BMap.Marker(point,{icon:markerIcon}); 
            map.addOverlay(marker);
            marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画  
            //map.panTo(point);                                                
        }

        if(address.attr('lng') && address.attr('lat')){
            point = new BMap.Point(address.attr('lng'),address.attr('lat'));
            drawMarker(point);
            geocoder.getLocation(point, function(addr) {
                addrPonit['coor'] = {lng:point.lng,lat:point.lat};
                geoCoor.attr({"lat": point.lat,"lng": point.lng});
            });                        
        }else{
            var addr = "";
            var prov = $("#province option:selected");
            var city = $("#city option:selected");
            var area = $("#area option:selected");
            if(prov.val()) addr = addr + prov.text()+"省";
            if(city.val()) addr = addr + city.text()+"市";
            if(area.val()) addr = addr + area.text()+"区";
            addr = addr + address.val();
            if(addr){
                geocoder.getPoint(addr, function(p) {
                    point = p ? p : point;
                    drawMarker(point);                       
                })                                                                            
            }else{
                drawMarker(point);                             
            }
        }

        //点击地图选择地理位置
        map.addEventListener("click", function(event) {
            point = event.point;
            map.clearOverlays();
            drawMarker(point); 
            geocoder.getLocation(point, function(addr) {
                addrPonit['coor'] = {lng:point.lng,lat:point.lat};
                geoCoor.attr({"lat": point.lat,"lng": point.lng});
                geoCoor.text(addr.address);
            });
        });

        //建立一个自动完成的查询对象
        var search = new BMap.Autocomplete({"input": "map-search","location": map});
        search.addEventListener("onconfirm", function(event) {
            var area = event.item.value;
            var addr = area.province+area.city+area.district+area.street+area.business;
            geocoder.getPoint(addr, function(point) {
                if (point) {
                    map.centerAndZoom(point, 16);
                    map.clearOverlays();
                    drawMarker(point);
                } else {
                    lib.popup.alert({text:'暂时无法获取您的位置'})
                }
            })
        });

        $("#pop-cancel-btn").off("click");
        $("#pop-cancel-btn").on('click',function(){
            $("#pop-wrapper").hide();
        });

        $("#pop-sure-btn").off("click");       
        $("#pop-sure-btn").on('click',function(){
            if(addrPonit.coor){
               $("#coorMarker").text("已标记");
               $("#coorMarkerInput").val("{'lng':"+addrPonit.coor.lng+",'lat':"+addrPonit.coor.lat+"}");
               $("#coorMarkerInput").blur();
               $("#pop-wrapper").hide();
            }else{
               lib.popup.alert({text:'未标记地图'});
            }
        });
    });
}