/* 
* @Author: anchen
* @Date:   2015-10-19 15:33:23
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-20 19:07:26
*/

(function(){
    var type = lib.query.type;
    var selectItemType = lib.query.selectItemType || 1; 

    if(type == 'add'){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data'));
        if(baseData.selectItemType == "3" && baseData.getItemTypes){
            $("#consumeItemsAll").attr('checked','checked');
        }        
        lib.ajat('#domid=form&tempid=form-t').template(baseData);    
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
        if($(this).val()=="3"){
            $('input.mobile-button').removeAttr('disabled');           
        }
        $(this).parent().next().removeAttr('disabled');
        $(this).parent().next().attr('required','required');
    });
    
    /**
     * selectItemType=='2' 添加手机号码
     * @return {[type]} [description]
     */
    $("#form").on('click','.mobile-button',function(){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data'));
        sessionStorage.setItem('add-base-data',JSON.stringify(baseData));
        window.location.href = "addMobile.html?type="+type+'&selectItemType='+selectItemType;
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
        location.href = $(this).attr('href')+"?type="+type+"&selectItemType="+selectItemType;        
    }); 

    /**
     * 仅允许输入int类型数据
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
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
        location.href = "addTicket.html?type="+type+"&selectItemType="+selectItemType;
    }        
})();