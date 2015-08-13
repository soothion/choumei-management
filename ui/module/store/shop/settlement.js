(function(){
    var type = lib.query.type;

    $(".flex-item a").on('click',function(e){
        e.preventDefault();
        location.href = $(this).attr('href') + "?type="+type;
    });

    lib.Form.prototype.save = function(data){
        var shopData = "";
        if(type === 'edit'){
            shopData = JSON.parse(sessionStorage.getItem('edit-shop-data'));
            shopData = $.extend({},shopData,data); 
        }
        if(type === 'add'){
            shopData = JSON.parse(sessionStorage.getItem('add-shop-data'));
            shopData = $.extend({},shopData,data);               
        }
        submit(shopData);
        document.body.onbeforeunload=function(){}
    }

    var submit = function(shopData){
        lib.ajax({
            type: "post",
            url : (type=="add"?"salon/save":"salon/update"),
            data: shopData    
        }).done(function(data, status, xhr){
            parent.lib.popup.result({
                bool:data.result == 1,
                text:(data.result == 1?"店铺信息提交成功":data.msg),
                time:2000,
                define:function(){
                    if(data.result == 1){
                        sessionStorage.removeItem('add-shop-data');
                        sessionStorage.removeItem('edit-shop-data');
                        document.body.onbeforeunload=function(){}
                        if(type === "edit") 
                            location.href="/module/shop/detail.html?type=detail&salonid="+shopData.salonid;
                        if(type === "add") 
                            location.href="/module/merchant/index.html";
                    }
                }
            });
           
        });
    } 
    
})()