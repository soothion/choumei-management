(function(){
    var type = lib.query.type;
    var selectItemType = ""; 

    if(type == 'add'){
        selectItemType = lib.query.selectItemType || 1; 
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data')) || {};
        lib.ajat('#domid=form&tempid=form-t').template(baseData);    
    }

    if(type == 'edit'){
        var editData = JSON.parse(sessionStorage.getItem('edit-base-data'));
        selectItemType = editData.selectItemType || 1; 
        lib.ajat('#domid=form&tempid=form-t').template(editData);
    }

    $("#form").on('click',".flex-item a",function(e){
        e.preventDefault();
        location.href = $(this).attr('href')+"?type="+type+"&selectItemType="+selectItemType;        
    });

    $("#form").on('click','#preview-btn',function(){
        var data = lib.getFormData($("#form"));     
        data.manager = $("input[value="+data.managerId+"]").next().text();
        if(type === 'add')  var previewData = JSON.parse(sessionStorage.getItem('add-base-data'));
        if(type === 'edit') var previewData = JSON.parse(sessionStorage.getItem('edit-base-data'));
        previewData = $.extend({},previewData,data);
        sessionStorage.setItem('preview-base-data',JSON.stringify(previewData));
        window.open("preview.html?type="+type);       
    })

    lib.Form.prototype.save = function(data){
        if(type == 'add'){    
            var addData = JSON.parse(sessionStorage.getItem('add-base-data'));
            addData = $.extend({},addData,data);
            sessionStorage.setItem('add-base-data',JSON.stringify(addData));             
        }
        if(type == 'edit'){
            var editData = JSON.parse(sessionStorage.getItem('edit-base-data'));
            editData = $.extend({},editData,data);
            var saveData = data;
            saveData.vcId = editData.vcId;
            sessionStorage.setItem('edit-base-data',JSON.stringify(editData));
            sessionStorage.setItem('edit-save-data',JSON.stringify(saveData));
        }
        location.href = "addUser.html?type="+type+"&selectItemType="+selectItemType;
    }    

})();