/* 
* @Author: anchen
* @Date:   2015-10-19 15:33:23
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-20 09:44:54
*/

(function(){
    var type = lib.query.type;
    var selectItemType = lib.query.selectItemType || 1; 

    if(type == 'add'){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data')) || {};
        lib.ajat('#domid=form&tempid=form-t').template(baseData);    
    }

    if(type == 'edit'){

    }

    $("#form").on('change','#consumeItems',function(){            
        if($(this).attr('checked')){
            $(this).removeAttr('checked');
            $("#consumeItemsDiv").hide();
            $("#consumeItemsDiv").find('input[type=checkbox]').attr("disabled","disabled")
        }else{
            $(this).attr('checked','checked');
            $("#consumeItemsDiv").show();
            $("#consumeItemsDiv").find('input[type=checkbox]').removeAttr("disabled");
        }
    });

    $("#form").on('click','.mobile-button',function(){
        window.location.href = "addMobile.html";
    })

    $("#form").on('click',".flex-item a",function(e){
        e.preventDefault();
        location.href = $(this).attr('href')+"?type="+type+"&selectItemType="+selectItemType;        
    });    

    $("#form").on('click','input[type=radio]',function(){
        $("#selectTip").remove();
        $('input.cascade').attr('disabled','disabled');
        $('input.cascade').removeAttr('required');

        $(this).parent().next().removeAttr('disabled');
        $(this).parent().next().attr('required','required');
    });

    $("#form").on('change','#vcodeCheckbox',function(){
        if($(this).attr('checked')){
            $(this).removeAttr('checked');
        }else{
            $(this).attr('checked','checked');
        }
    });  

    $("#form").on('change','input[type=radio]',function(){
        $("#form").find("span.control-help").hide();
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

    lib.Form.prototype.save = function(data){
        if(data.getItemTypes && data.getItemTypes.length>0){
            data.getItemTypes = data.getItemTypes.join(",");
        }
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data'));
        baseData = $.extend({},baseData,data);
        sessionStorage.setItem('add-base-data',JSON.stringify(baseData));
        location.href = "addTicket.html?type="+type+"&selectItemType="+selectItemType;
    }        
})();