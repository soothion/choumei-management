/* 
* @Author: anchen
* @Date:   2015-10-19 15:33:23
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-23 17:51:21
*/

(function(){
    var type = lib.query.type;
    var selectItemType = lib.query.selectItemType || 1; 

    if(type == 'add'){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data'));
        lib.ajat('#domid=form&tempid=form-t').template(baseData);    
        if(baseData.selectItemType == "3" && baseData.getItemTypes){
            $("#consumeItemsAll").attr('checked','checked');
        }       
    }

    if(type == 'edit'){
        var editData = JSON.parse(sessionStorage.getItem('edit-base-data'));
        lib.ajat('#domid=form&tempid=form-t').template(editData);
        if(editData.selectItemType == "3" && editData.getItemTypes){
            $("#consumeItemsAll").attr('checked','checked');
        }
    }

    /**
     * selectItemType=='2' 指定用户事件单选按钮事件控制
     * @return {[type]} [description]
     */
    $("#form").on('change','#consumeItems',function(){            
        if($(this).attr('checked')){
            $(this).removeAttr('checked');
            $("#consumeItemsDiv").hide();
            $("#consumeItemsDiv").find('span.control-help').hide();
            $("#consumeItemsDiv").find('input[type=checkbox]').attr("disabled","disabled")
        }else{
            $(this).attr('checked','checked');
            $("#consumeItemsDiv").show();
            $("#consumeItemsDiv").find('input[type=checkbox]').removeAttr("disabled");
        }
    });

    /**
     * selectItemType=='2' 指定用户事件单选按钮事件控制
     * @return {[type]} [description]
     */
    $("#form").on('click','input[type=radio]',function(){
        $('input.cascade').attr('disabled','disabled');
        $('input.cascade').removeAttr('required');
        $('input[name=code]').val("");
        if($(this).val()=="3"){
            $('input.mobile-button').removeAttr('disabled');         
        }
        $(this).parent().next().removeAttr('disabled');
        $(this).parent().next().attr('required','required');
        $('span.control-help').hide();
        var data = JSON.parse(sessionStorage.getItem('add-base-data'));  
        data.phoneList = [];
        data.code = "";
        sessionStorage.setItem('add-base-data',JSON.stringify(data));   
    });
    
    /**
     * selectItemType=='2' 添加手机号码
     * @return {[type]} [description]
     */
    $("#form").on('click','.mobile-button',function(){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data'));
        baseData.selectUseType = "3";
        sessionStorage.setItem('add-base-data',JSON.stringify(baseData));
        window.location.href = "addMobile.html?type="+type+'&selectItemType='+selectItemType;
    });

    $("#form").on('pass','input[data-check]',function(){
        var self = this;
        lib.ajax({
            type: "post",
            url : 'platform/checkSerial',
            async:false,
            data: {type:$(self).data('check'),code:$(self).val()}    
        }).done(function(data, status, xhr){
            if(data.result == "1"){
                if(data.data.exists == "0"){
                    $(self).next().text($(self).attr('placeholder')+'不存在').show();
                }
            }
        })     
    })

    /**
     * selectItemType=='3' 全平台用户事件控制
     * @return {[type]} [description]
     */ 
    $("#form").on('change','#consumeItemsAll',function(){
        if($(this).attr('checked')){
            $(this).removeAttr('checked');
            $("#consumeItemsAllDiv").find('span.control-help').hide();
            $("#consumeItemsAllDiv").find('input[type=checkbox]').attr("disabled","disabled");
        }else{
            $(this).attr('checked','checked');
            $("#consumeItemsAllDiv").find('input[type=checkbox]').removeAttr("disabled");
        }        
    });

    /**
     * selectItemType=='4' H5用户事件控制
     * @return {[type]} [description]
     */
    $("#form").on('change','#H5UsercheckBox',function(){
        if($(this).attr('checked')){
            $(this).removeAttr('checked');
            $(this).parent().next().attr("disabled","disabled");
        }else{
            $(this).attr('checked','checked');
            $(this).parent().next().removeAttr("disabled");
        }        
    })

    /**
     * 顶部tab导航
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    $("#form").on('click',".flex-item a",function(e){
        e.preventDefault();
        var selectItemType = 1;
        if(type == 'add'){
            var data = JSON.parse(sessionStorage.getItem('add-base-data'));
            if(data.selectItemType){
                selectItemType = data.selectItemType;
            }  
        }
        
        if(type == 'edit'){
            var data = JSON.parse(sessionStorage.getItem('edit-base-data'));
            if(data.selectItemType){
                selectItemType = data.selectItemType;
            } 
        }
        location.href = $(this).attr('href')+"?type="+type+"&selectItemType="+selectItemType;        
    }); 

    /**
     * 用户类型tab切换
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    $("#form").on('click',"a.tab-menus",function(e){
        if(type == 'add'){
            var data = JSON.parse(sessionStorage.getItem('add-base-data'));  
            data.getItemTypes   = "";
            data.enoughMoeny    = "";
            data.phoneList      = [];
            data.code           = "";
            data.selectUseType  = 1;
            data.selectItemType = 1;
            sessionStorage.setItem('add-base-data',JSON.stringify(data));           
        }
    })
    
    lib.Form.prototype.save = function(data){
        if(data.getItemTypes && data.getItemTypes.length>0){
            data.getItemTypes = data.getItemTypes.join(",");
        }
        if(type == 'add'){    
            var addData = JSON.parse(sessionStorage.getItem('add-base-data'));
            addData = $.extend({},addData,data);
            sessionStorage.setItem('add-base-data',JSON.stringify(addData));             
        }
        if(type == 'edit'){
            var editData = JSON.parse(sessionStorage.getItem('edit-base-data'));
            editData = $.extend({},editData,data);
            sessionStorage.setItem('edit-base-data',JSON.stringify(editData)); 
           
            // var saveData = JSON.parse(sessionStorage.getItem('edit-save-data'));
            // saveData = $.extend({},saveData,data);
            // sessionStorage.setItem('edit-save-data',JSON.stringify(saveData));                 
        }

        location.href = "addTicket.html?type="+type+"&selectItemType="+selectItemType;
    }        
})();