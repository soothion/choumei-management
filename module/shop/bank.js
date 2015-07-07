/* 
* @Author: anchen
* @Date:   2015-07-07 10:22:30
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-07 10:25:36
*/

(function(){
    var type = utils.getSearchString("type");

    if(type && type === 'edit'){
        var data = JSON.parse(sessionStorage.getItem('edit-shop-data'));       
        lib.ajat('#domid=form&tempid=form-t').template(data);     
    }
    
    if(type && type === 'add'){
        lib.ajat('#domid=form&tempid=form-t').template({});    
    }

    $(".flex-item a").on('click',function(e){
        e.preventDefault();
        location.href = "add.html?type="+type;
    });

    lib.Form.prototype.save = function(data){
        debugger;
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
        location.href = "assess.html?type="+type;
    }
})()