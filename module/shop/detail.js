/* 
* @Author: anchen
* @Date:   2015-07-06 16:48:38
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-06 18:02:16
*/

(function(){
    var type = utils.getSearchString("type");
    var salonId = utils.getSearchString("salonid");
    if(type && type === 'detail'){
        var promise = lib.ajat('salon/getSalon?salonid='+salonId+'#domid=table-wrapper&tempid=table-t').render();
        promise.done(function(data){
            var str = JSON.stringify(data.data);
            sessionStorage.setItem('edit-shop-data',str);          
        });
    }

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