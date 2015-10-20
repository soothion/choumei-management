(function(){
    var type = lib.query.type;
    var selectItemType = lib.query.selectItemType || 1; 

    if(type == 'add'){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data')) || {};
        lib.ajat('#domid=form&tempid=form-t').template(baseData);    
    }

    if(type == 'edit'){
        
    }

    $("#form").on('click',".flex-item a",function(e){
        e.preventDefault();
        location.href = $(this).attr('href')+"?type="+type+"&selectItemType="+selectItemType;        
    });

    lib.Form.prototype.save = function(data){
        sessionStorage.setItem('add-base-data',JSON.stringify(data));
        location.href = "addUser.html?type="+type+"&selectItemType="+selectItemType;
    }    

})();