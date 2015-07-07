/* 
* @Author: anchen
* @Date:   2015-07-07 17:22:33
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-07 18:21:33
*/

(function(){
    var type = utils.getSearchString("type");

    if(type && type === 'edit'){
        var data = JSON.parse(sessionStorage.getItem('edit-shop-data'));       
        lib.ajat('#domid=form&tempid=form-t').template(data);
        if(data.salonType && data.salonType.length >0){
            data.salonType.forEach(function(value,index){
                $(":checkbox[value='"+value+"']").attr('checked',true);
            })
        }      
    }

    if(type && type === 'add'){
        var shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
        lib.ajat('#domid=form&tempid=form-t').template(shopData);
        if(shopData.salonType && shopData.salonType.length >0){
            shopData.salonType.forEach(function(value,index){
                $(":checkbox[value='"+value+"']").attr('checked',true);
            })
        }     
    }

    $(".flex-item a").on('click',function(e){
        e.preventDefault();
        location.href = $(this).attr('href') + "?type="+type;
    });

    lib.Form.prototype.save = function(data){
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