/* 
* @Author: anchen
* @Date:   2015-10-23 17:36:01
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-23 18:54:23
*/

(function(){

    var type = lib.query.type;

    var selectItemType = lib.query.selectItemType; 

    if(type == 'add'){
        var baseData = JSON.parse(sessionStorage.getItem('add-base-data'));
        baseData = setData(baseData);
        lib.ajat('#domid=table-wrapper&tempid=table-t').template(baseData);     
    }

    if(type == 'edit'){
        var editData = JSON.parse(sessionStorage.getItem('edit-base-data'));
        editData = setData(editData);
        lib.ajat('#domid=table-wrapper&tempid=table-t').template(editData);
    }

    $("#table-wrapper").on('click','button.btn-primary',function(){
        var submitData = "";
        if(type == 'add')  submitData = JSON.parse(sessionStorage.getItem('add-base-data'));
        if(type == 'edit') submitData = JSON.parse(sessionStorage.getItem('edit-base-data'));
        if(submitData.getTimeStart){
            submitData.getTimeStart = submitData.getTimeStart + " 00:00:00";
        }
        if(submitData.getTimeEnd){
            submitData.getTimeEnd = submitData.getTimeEnd + " 23:59:59";
        }
        if(submitData.addActLimitStartTime){
            submitData.addActLimitStartTime = submitData.addActLimitStartTime  + " 00:00:00";
        }
        if(submitData.addActLimitEndTime){
            submitData.addActLimitEndTime = submitData.addActLimitEndTime  + " 23:59:59";
        }
        if(submitData.limitItemTypes){
            submitData.limitItemTypes = ","+submitData.limitItemTypes.join(",")+",";
        }
        if(submitData.useLimitTypes){
            submitData.useLimitTypes = submitData.useLimitTypes[0];
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
                    sessionStorage.removeItem('platformItemTypes');
                    document.body.onbeforeunload=function(){}
                    if(type=='add')  location.href="/module/marketing/ticket/platformAct/index.html";
                    if(type=='edit') location.href="/module/marketing/ticket/platformAct/detail.html?id="+submitData.vcId;
                }
            });          
          }
        })        
    });

    function setData (data){
        var items = JSON.parse(sessionStorage.getItem('platformItemTypes'));
        if(data.selectItemType == "1" || data.selectItemType=="2"){
            data.selectItem = data.selectUseType;
        }
        if(data.selectItemType == "3"){
            data.selectItem = 7;
        }
        if(data.selectItemType == "4"){
            data.selectItem = 8;
        }
        var items = JSON.parse(sessionStorage.getItem("platformItemTypes"));         
        var itemTypeArr = [] , limitItemArr = [];
        if(data.getItemTypes && typeof data.getItemTypes=='string'){
            var arr = data.getItemTypes.split(',');
            arr.forEach(function(item,i){
               items.forEach(function(obj,i){
                if(item == obj.typeid){
                    itemTypeArr.push(obj);
                }
                });
            });
        }

        if(data.limitItemTypes && typeof data.limitItemTypes == 'string'){
            var arr = data.getItemTypes.split(',');
            arr.forEach(function(item,i){
                items.forEach(function(obj,i){
                    if(item == obj.typeid){
                        limitItemArr.push(obj);
                    }
                });
            });
        }
        data.itemTypeArr = itemTypeArr;
        data.limitItemArr= limitItemArr;  
        return data;     
    }
})()