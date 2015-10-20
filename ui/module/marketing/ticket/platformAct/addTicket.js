/* 
* @Author: anchen
* @Date:   2015-10-19 17:28:25
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-20 09:44:50
*/

(function(){
    var type = lib.query.type;
    var selectItemType = lib.query.selectItemType || 1; 
    if(type == 'add'){
        var ticketData = JSON.parse(sessionStorage.getItem('add-ticket-data')) || {};
        lib.ajat('#domid=form&tempid=form-t').template(ticketData);    
    }

    if(type == 'edit'){

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

    lib.Form.prototype.save = function(data){
      if(data.limitItemTypes){
         data.limitItemTypes = data.limitItemTypes.join(",");
      }
      if(data.useLimitTypes){
        data.useLimitTypes = data.useLimitTypes[0];
      }
      var basaData = JSON.parse(sessionStorage.getItem('add-base-data'));
      basaData = $.extend({},basaData,data);

      lib.ajax({
          type: "post",
          url : (type=="add"?"paltfrom/add":"paltfrom/update"),
          data: basaData    
      }).done(function(data, status, xhr){
         if(data.result == 1){
            parent.lib.popup.result({
                text:"店铺信息提交成功",
                time:2000,
                define:function(){
                    sessionStorage.removeItem('add-base-data');  
                    document.body.onbeforeunload=function(){}
                }
            });          
         }
      })
    }      
})()