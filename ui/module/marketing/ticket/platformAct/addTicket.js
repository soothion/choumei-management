/* 
* @Author: anchen
* @Date:   2015-10-19 17:28:25
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-21 18:18:42
*/

(function(){
    var type = lib.query.type;
    var selectItemType = lib.query.selectItemType || 1; 
    if(type == 'add'){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data'));
        lib.ajat('#domid=form&tempid=form-t').template(baseData);    
    }

    if(type == 'edit'){
        var editData = JSON.parse(sessionStorage.getItem('edit-base-data'));
        lib.ajat('#domid=form&tempid=form-t').template(editData);
    }

    $("#form").on('click','.ticketNum',function(){
        if($(this).val()=="1"){
           $("#ticketNumInput").removeAttr('disabled','disabled');
           $(".ticketNumHidden").attr('disabled','disabled');
        }else{
           $("#ticketNumInput").attr('disabled');
           $(".ticketNumHidden").removeAttr('disabled');
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

    $("#form").on('click',".flex-item a",function(e){
        e.preventDefault();
        location.href = $(this).attr('href')+"?type="+type+"&selectItemType="+selectItemType;        
    });

    $("#form").on('keydown',"input[pattern='number']",function(e){
        var key = e.which;
        //alert(key)
        if ((key > 95 && key < 106) || //小键盘上的0到9  
            (key > 47 && key < 58) || //大键盘上的0到9  
            key == 8 || key == 116 || key == 9 || key == 46 || key == 37 || key == 39
            //不影响正常编辑键的使用(116:f5;8:BackSpace;9:Tab;46:Delete;37:Left;39:Right;)  
        ) {
            return true;
        } else {
            return false;
        }
    }); 

  
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

    $("#form").on('click','#preview-btn',function(){
        var data = lib.getFormData($("#form"));  
        if($.isArray(data.limitItemTypes)){
            var limitItemArr = [];
            data.limitItemTypes.forEach(function(item,index){
                var input = $("#itemType").find("input[value="+item+"]");
                var obj = {value:input.val(),name:input.next().text()};
                limitItemArr.push(obj);
            })
            data.limitItemArr = limitItemArr;
        }
        if(type === 'add')  var previewData = JSON.parse(sessionStorage.getItem('add-base-data'));
        if(type === 'edit') var previewData = JSON.parse(sessionStorage.getItem('edit-base-data'));
        previewData = $.extend({},previewData,data);
        sessionStorage.setItem('preview-base-data',JSON.stringify(previewData));
        window.open("preview.html?type="+type);       
    })

    lib.Form.prototype.save = function(data){
      data.checkTotalNumber = undefined;
      data.avaDate = undefined;

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

      var submitData = {};
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

      if(submitData.phoneList && $.isArray(submitData.phoneList )){
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