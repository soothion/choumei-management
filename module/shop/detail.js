/* 
* @Author: anchen
* @Date:   2015-07-06 16:48:38
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-08 19:55:57
*/

(function(){
    var type = utils.getSearchString("type");
    var salonId = utils.getSearchString("salonid");

    var selectSalonType = function(data){
        if(data.salonType){
            var arr = data.salonType.split("_");
            arr.forEach(function(value,index){
                $(":checkbox[value='"+value+"']").attr('checked',true).show().next().show(); 
            })       
        }
    }

    var createScript = function(){
        var el = document.createElement("script");
        el.setAttribute('src','http://api.map.baidu.com/api?v=2.0&ak=F360d7e9ea3c3ecb5b9b9f2b530be8f4&callback=renderMap()');
        document.getElementsByTagName("head")[0].appendChild(el);
    }

    //查看详情
    if(type === 'detail'){
        var promise = lib.ajat('salon/getSalon?salonid='+salonId+'#domid=table-wrapper&tempid=table-t').render();
        promise.done(function(data){
            var str = JSON.stringify(data.data);
            sessionStorage.setItem('edit-shop-data',str);    
            selectSalonType(data.data);
        });
        createScript();
    }

    if(type === "preview"){
        var data = JSON.parse(sessionStorage.getItem('preview-shop-data'));
        var conArr = JSON.parse(localStorage.getItem("contractPicUrl")); 
        var licArr = JSON.parse(sessionStorage.getItem("licensePicUrl"));
        var corArr = JSON.parse(sessionStorage.getItem("corporatePicUrl")); 
        if(conArr && conArr.length > 0){
            data.contractPicUrl = localStorage.getItem("contractPicUrl");
        }
        if(licArr && licArr.length > 0){
            data.licensePicUrl = sessionStorage.getItem("licensePicUrl");
        }  
        if(corArr && corArr.length > 0){
            data.corporatePicUrl = sessionStorage.getItem("corporatePicUrl");
        }     
        lib.ajat('#domid=table-wrapper&tempid=table-t').template(data);
        createScript();
        $(".btn-group").hide();
        selectSalonType(data);         
    }

    $("#table-wrapper").delegate('#stop_cooperation_btn','click',function(){
        var status = $(this).attr("status");
        var data   = {salonid : $(this).attr("salonid")};
        var msg    = "";
        if(status == "0"){
            data.type = 2;
            msg = "终止合作";
        }else{
            data.type = 1;
            msg = "恢复合作";
        }

        $.ajax({
            method: "post",
            dataType: "json",
            async: false,
            data : data,
            url : cfg.getHost()+"salon/endCooperation"
        }).done(function(data, status, xhr){
            if(data.result == 1){
                lib.popup.tips({text:"信息提交成功！"});
                $(this).text(msg);
            }else{
                lib.popup.tips({text:data.msg ||"信息提交失败！"});
            }
        }).fail(function(xhr, status){
            var msg = "请求失败，请稍后再试!";
            if (status === "parseerror") msg = "数据响应格式异常!";
            if (status === "timeout")    msg = "请求超时，请稍后再试!";
            if (status === "offline")    msg = "网络异常，请稍后再试!";
            lib.popup.tips({text:msg});
        });
    })

    $("#table-wrapper").delegate('#remove_stop_btn','click',function(){
        var data   = {salonid : $(this).attr("salonid")};
        $.ajax({
            method: "post",
            dataType: "json",
            async: false,
            data : data,
            url : cfg.getHost()+"salon/del"
        }).done(function(data, status, xhr){
            if(data.result == 1){
                lib.popup.tips({text:"信息提交成功！"});
                location.href="index.html";
            }else{
                lib.popup.tips({text:data.msg ||"信息提交失败！"});
            }
        }).fail(function(xhr, status){
            var msg = "请求失败，请稍后再试!";
            if (status === "parseerror") msg = "数据响应格式异常!";
            if (status === "timeout")    msg = "请求超时，请稍后再试!";
            if (status === "offline")    msg = "网络异常，请稍后再试!";
            lib.popup.tips({text:msg});
        });
    })

    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        slidesPerView: 1,
        paginationClickable: true,
        spaceBetween: 30,
        loop: true
    });
})();

    function renderMap(){
        //在指定的容器内创建地图实例  
        var map = new BMap.Map("mapContent");
        //创建一个地理坐标类 
        var point = new BMap.Point(113.941893,22.535644);
        //初始化地图位置（未进行初始化的地图将不能进行任何操作）
        map.centerAndZoom(point,12);
        //将平移缩放控件添加到地图上
        map.addControl(new BMap.NavigationControl());
        //启用滚轮放大缩小功能
        map.enableScrollWheelZoom(true);
        //此处是一个补丁，由于未知的原因直接new Marker对象添加的标注
        //标注的图标很小，所以这里直接用自己的图标            
        var markerIcon = new BMap.Icon("../../images/marker.png", new BMap.Size(39,25),{
            anchor : new BMap.Size(10,25) 
        });
        //根据point创建一个图像标注覆盖物实例
        var marker = new BMap.Marker(point,{icon:markerIcon}); 
        //将覆盖物添加到地图中
        map.addOverlay(marker);
        //跳动的动画   
        marker.setAnimation(BMAP_ANIMATION_BOUNCE);
                
        $(".map-search-bar").on('click',function(){
        window.open("http://map.baidu.com/?latlng=22.535644,113.941893&title=我们位置&content=南山科技中一路华强高新发展大楼&autoOpen=true");            
      })           
    }