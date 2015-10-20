(function(){
    var type = lib.query.type;
    var selectItemType = lib.query.selectItemType || 1; 

    if(type == 'add'){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data')) || {};
        lib.ajat('#domid=form&tempid=form-t').template(baseData);    
    }

    if(type == 'edit'){
        var editDataStr = sessionStorage.getItem('edit-base-data');
        if(editDataStr){
            var editData = JSON.parse(eidtDataStr);
            lib.ajat('#domid=form&tempid=form-t').template(JSON.parse(eidtDataStr));   
        }else{
            var promise = lib.ajat('platform/getInfo/'+lib.query.id+'#domid=form&tempid=form-t').render();
            promise.done(function(data){
                sessionStorage.setItem('edit-base-data',JSON.stringify(data.data));  
            });
        }
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