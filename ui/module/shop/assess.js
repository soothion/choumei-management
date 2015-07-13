/* 
* @Author: anchen
* @Date:   2015-07-07 17:22:33
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-08 18:49:53
*/

(function(){
    var type = utils.getSearchString("type");

    if(type === 'edit'){
        var shopData = JSON.parse(sessionStorage.getItem('edit-shop-data'));       
        lib.ajat('#domid=form&tempid=form-t').template(shopData);
        if(shopData.salonType){
            var arr = shopData.salonType.split("_");
            arr.forEach(function(value,index){
                $(":checkbox[value='"+value+"']").attr('checked',true);
            })
        }      
    }

    if(type === 'add'){
        var shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
        lib.ajat('#domid=form&tempid=form-t').template(shopData);
        if(shopData.salonType){
            var arr = shopData.salonType.split("_");
            arr.forEach(function(value,index){
                $(":checkbox[value='"+value+"']").attr('checked',true);
            })
        }     
    }

    $(".flex-item a").on('click',function(e){
        e.preventDefault();
        location.href = $(this).attr('href') + "?type="+type;
    });

    $("#preview-btn").on('click',function(){
        var data = lib.getFormData($("#form"));
        if($.inArray(data.salonType)){
            data.salonType = data.salonType.join("_");
        } 
        if(type === 'edit') var shopData = JSON.parse(sessionStorage.getItem('edit-shop-data'));
        if(type === 'add')  var shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
        shopData = $.extend({},shopData,data);
        sessionStorage.setItem('preview-shop-data',JSON.stringify(shopData));
        window.open("detail.html?type=preview");
    })

    lib.Form.prototype.save = function(data){
        if($.isArray(data.salonType)){
            data.salonType = data.salonType.join("_");
        } 
        if(type && type === 'edit'){
            var shopData = JSON.parse(sessionStorage.getItem('edit-shop-data'));
            shopData = $.extend({},shopData,data);
            sessionStorage.setItem('edit-shop-data',JSON.stringify(shopData));   
        }

        if(type && type === 'add'){
            var shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
            shopData = $.extend({},shopData,data);
            sessionStorage.setItem('add-shop-data',JSON.stringify(shopData));            
        }

        location.href = "upload.html?type="+type;
    }     
})();