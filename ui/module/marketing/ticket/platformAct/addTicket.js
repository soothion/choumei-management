/* 
* @Author: anchen
* @Date:   2015-10-19 17:28:25
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-22 16:30:01
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
      data.checkTotalNumber = undefined;
      data.avaDate = undefined;
      var submitData = {};
      if(data.getTimeStart){
        data.getTimeStart = data.getTimeStart + " 00:00:00";
      }
      if(data.getTimeEnd){
         data.getTimeEnd = data.getTimeEnd + " 23:59:59";
      }
      if(data.addActLimitStartTime){
         data.addActLimitStartTime = data.addActLimitStartTime  + " 00:00:00";
      }
      if(data.addActLimitEndTime){
         data.addActLimitEndTime = data.addActLimitEndTime  + " 23:59:59";
      }
      if(data.limitItemTypes){
         data.limitItemTypes = ","+data.limitItemTypes.join(",")+",";
      }
      if(data.useLimitTypes){
        data.useLimitTypes = data.useLimitTypes[0];
      }
      if(type == 'add'){    
          var addData = JSON.parse(sessionStorage.getItem('add-base-data'));
          submitData = $.extend({},addData,data);           
      }
      if(type == 'edit'){
          var saveData = JSON.parse(sessionStorage.getItem('edit-save-data'));
          submitData = $.extend({},saveData,data);               
      }
      if(submitData.phoneList && $.isArray(submitData.phoneList)){
         submitData.phoneList = submitData.phoneList.join(",");
      }
      if(submitData.getItemTypes){
         submitData.getItemTypes = ","+submitData.getItemTypes+",";
      }      
      lib.ajax({
          type: "post",
          url : (type=="add"?"platform/add":"platform/editConf"),
          data: submitData    
      }).done(function(data, status, xhr){
          if(data.result == 1){
            parent.lib.popup.result({
                text:"店铺信息提交成功",
                time:2000,
                define:function(){
                    sessionStorage.removeItem('add-base-data'); 
                    sessionStorage.removeItem('edit-base-data');
                    sessionStorage.removeItem('edit-save-data');
                    document.body.onbeforeunload=function(){}
                    if(type=='add')  location.href="/module/marketing/ticket/platformAct/index.html";
                    if(type=='edit') location.href="/module/marketing/ticket/platformAct/detail.html?id="+submitData.vcId;
                }
            });          
          }
      })
    }      
})()