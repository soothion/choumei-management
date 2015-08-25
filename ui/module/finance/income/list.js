/* 
* @Author: anchen
* @Date:   2015-08-19 15:54:43
* @Last Modified by:   anchen
* @Last Modified time: 2015-08-25 17:13:00
*/

(function(){
    $(".table").delegate('button[type="button"]', 'click', function(event) {
        var arr = [];      
        var amount = 0;    
        $('tbody input[type="checkbox"]:checked').each(function(index,obj){
          arr.push($(obj).data("id"));
          amount += (+$(obj).data("amount"));
        });
        if(arr.length > 0){
            parent.lib.popup.confirm({
                text:'你正在对'+arr.length+'家店铺确认返佣,返佣总额￥'+amount+',是否继续?',
                define:function(){
                    lib.ajax({
                        type: "post",
                        data : {rebate : arr},
                        url : "rebate/confirm"
                    }).done(function(data, status, xhr){
                        parent.lib.popup.result({
                            bool:data.result == 1,
                            text:(data.result == 1 ? "返佣成功" : data.msg),
                            time:2000,
                            define:function(){
                                //lib.ajat('rebate/index?<%=query._%>#domid=table&tempid=table-t').render();
                                $(window).trigger("hashchange");
                            }
                        });
                    });                          
                }
            });
        }
    }); 

    var uploader = WebUploader.create({
        swf   : '../../js/Uploader.swf',
        server: cfg.getHost() + "rebate/upload",
        pick  : '#import',
        resize: false,
        auto  : true,
        fileNumLimit : 1,
        fileSingleSizeLimit : 10*1024*1024,
        fileVal:'rebate',
        accept :{
            title: 'Excel',
            extensions: 'xls',
            mimeTypes: 'application/vnd.ms-excel'                
        }                
    });

    uploader.on('uploadError',function(){
        parent.lib.popup.result({
            bool:true,
            text:"数据导入出错！",
            time:2000
        });
    })

    uploader.on('uploadSuccess',function(file,response){
        uploader.removeFile(file,true);
        uploader.reset();
        parent.lib.popup.result({
            bool:response.result  == 1,
            text:(response.result == 1 ? "数据导入成功！" : (response.msg ||"数据导入失败！")),
            time:2000
        });
    }); 

    uploader.on('uploadFinished',function(){
        lib.ajat('rebate/index?<%=query._%>#domid=table&tempid=table-t').render();
    });

    uploader.on('error',function(type){
        if(type == "Q_EXCEED_NUM_LIMIT") lib.popup.alert({text:'文件数量过大'});
        if(type == "Q_EXCEED_SIZE_LIMIT ") lib.popup.alert({text:'文件过大'});
        if(type == "Q_TYPE_DENIED ") lib.popup.alert({text:'不支持你选择的类型文件'});
    }) 
  
})();