/* 
* @Author: anchen
* @Date:   2015-07-07 10:22:30
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-08 18:22:46
*/

(function(){
    var type = utils.getSearchString("type");

    if(type === 'edit'){
        var data = JSON.parse(sessionStorage.getItem('edit-shop-data'));       
        lib.ajat('#domid=form&tempid=form-t').template(data);     
    }
    
    if(type === 'add'){
        var shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
        lib.ajat('#domid=form&tempid=form-t').template(shopData);    
    }

    $(".flex-item a").on('click',function(e){
        e.preventDefault();
        location.href = "add.html?type="+type;
    });

    $("#preview-btn").on('click',function(){
        var data = lib.getFormData($("#form"));
        if(type === 'edit') var shopData = JSON.parse(sessionStorage.getItem('edit-shop-data'));
        if(type === 'add')  var shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
        shopData = $.extend({},shopData,data);
        sessionStorage.setItem('preview-shop-data',JSON.stringify(shopData));
        window.open("detail.html?type=preview");
    })

    lib.Form.prototype.save = function(data){
        if(type === 'edit'){
            var shopData = JSON.parse(sessionStorage.getItem('edit-shop-data'));
            shopData = $.extend({},shopData,data);
            sessionStorage.setItem('edit-shop-data',JSON.stringify(shopData));   
        }
        if(type === 'add'){
            var shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
            shopData = $.extend({},shopData,data);
            sessionStorage.setItem('add-shop-data',JSON.stringify(shopData));            
        }
        location.href = "assess.html?type="+type;
    }
})()