/* 
* @Author: anchen
* @Date:   2015-10-19 17:28:25
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-23 18:36:29
*/

(function(){
    var type = lib.query.type;

    var selectItemType = lib.query.selectItemType; 

    if(type == 'add'){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data'));
        lib.ajat('#domid=form&tempid=form-t').template(baseData);     
    }

    if(type == 'edit'){
        var editData = JSON.parse(sessionStorage.getItem('edit-base-data'));
        lib.ajat('#domid=form&tempid=form-t').template(editData);
    }

    /**
     * 券总数操作事件
     * @return {[type]} [description]
     */
    $("#form").on('click','input[name=checkTotalNumber]',function(){
        if($(this).val()=="1"){
           $("#ticketNumInput").removeAttr('disabled','disabled');
           $("#ticketNumHidden").attr('disabled','disabled');
        }else{
           $("#ticketNumInput").attr('disabled','disabled');
           $("#ticketNumHidden").removeAttr('disabled');
        }
    })

    $("#form").on('input','.nonzero',function(){
        if($(this).val() == "0"){
          $(this).val("");
        }
    })

    $("#form").on('change','#smsControl',function(){
        if($(this).attr('checked')){
           $(this).removeAttr('checked');
           $("#smsTextArea").attr('disabled','disabled');
        }else{
           $(this).attr('checked','checked');
           $("#smsTextArea").removeAttr('disabled');
        }
    })

    $("#form").on('change','input.start',function(){
        var td = $(this).parent();
        if($(this).val()){
            td.find('input.end').attr('min',$(this).val());          
        }
    })

    $("#form").on('change','input.end',function(){
        var td = $(this).parent();
        if($(this).val()){
            td.find('input.start').attr('max',$(this).val());          
        }
    })

    $("#form").on('click','.avaDateRadio',function(){
        if($(this).val()=="1"){
           $("#avaDateStart").removeAttr('disabled');
           $("#avaDateEnd").removeAttr('disabled');
           $("#avaDay").attr('disabled','disabled');
        }else{
           $("#avaDateStart").attr('disabled','disabled');
           $("#avaDateEnd").attr('disabled','disabled');
           $("#avaDay").removeAttr('disabled');
        }
    })

    $("#form").on('click',".flex-item a",function(e){
        e.preventDefault();
        location.href = $(this).attr('href')+"?type="+type+"&selectItemType="+selectItemType;        
    });    

    $("#form").on('click','#preview-btn',function(){
        var data = lib.getFormData($("#form"));  
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
            sessionStorage.setItem('edit-base-data',JSON.stringify(editData));                          
        }   
        location.href = "preview.html?type="+type+"&selectItemType="+selectItemType;
    }      
})()