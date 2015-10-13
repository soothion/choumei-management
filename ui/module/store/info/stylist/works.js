/* 
* @Author: anchen
* @Date:   2015-10-12 13:59:43
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-13 09:31:43
*/

(function(){
   $("#works-wrapper").on("click",".control-thumbnails-remove",function(){
        var self = $(this);
        parent.lib.popup.confirm({
            text:"正在删除此图片，是否继续?",
            define:function(){
                var id  = self.closest('.control-thumbnails').data("id"); 
                var sib = self.parent().siblings();
                var str = "";
                var arr = []; 
                sib.each(function(i,item){
                    var obj = {
                        "thumbimg" : $(item).find("img").attr("src"),
                        "img"      : $(item).find("img").data("original")
                    };
                    arr.push(obj);                        
                })
                if(arr.length > 0){
                    str = JSON.stringify(arr);
                }          
                lib.ajax({
                    type: "post",
                    url : "Works/del/"+id,
                    data: {img : str}    
                }).done(function(data, status, xhr){
                    if(data.result == 1){
                        parent.lib.popup.result({
                            text:"操作成功！",
                            define:function(){
                                 $(window).trigger('hashchange');  
                            }
                        });                
                    }
                   
                });
            }
        });                                       
   });

   $("#works-wrapper").on("click",".del",function(){ 
        var self = this;
        parent.lib.popup.confirm({
            text:"正在删除作品，是否继续?",
            define:function(){
                var id = $(self).parent().next().data("id");
                lib.ajax({
                    type: "post",
                    url : "Works/del_list/"+id 
                }).done(function(data, status, xhr){
                    if(data.result == 1){
                        parent.lib.popup.result({
                            text:"操作成功！",
                            define:function(){
                                $(window).trigger('hashchange');    
                            }
                        });                
                    }                   
                }); 
            }
        });                                       
    });

    $("#works-wrapper").on('click','.control-thumbnails-before',function(){
        var $this=$(this);
        var thumbnail=$this.closest('.control-thumbnails-item');
        var prev=thumbnail.prev('.control-thumbnails-item')
        if(prev.length==1){
            thumbnail.after(prev);
            eidt($this);
        }
    });

    $("#works-wrapper").on('click','.control-thumbnails-after',function(){
         var $this=$(this);
         var thumbnail=$this.closest('.control-thumbnails-item');
         var next=thumbnail.next('.control-thumbnails-item')
         if(next.length==1){
             thumbnail.before(next);
             eidt($this);
         }
    });                

    function eidt(self){
       var thumbnail=self.closest('.control-thumbnails');
       var arr = [];
       thumbnail.find("img").each(function(i,item){
            var obj = {
                "thumbimg" : $(item).attr("src"),
                "img"      : $(item).data("original")
            };
            arr.push(obj);                        
        })
        lib.ajax({
            type: "post",
            url : "Works/update/"+thumbnail.data("id"),
            data:{img:JSON.stringify(arr)}
        }).done(function(data, status, xhr){
                  
        }); 

          
    }

    $("#add_works_btn").on('click',function(){
        lib.popup.box({
           width:$(window).width(),
           height:$(window).height(),
           title:'<h1>新增作品</h1>',
           content:$("#box").css("display","").html(),
           complete : function(){
            $('.popup #imagesUpload').attr('id',"worksUpload");
            $('.popup #submitBtn').on('click',function(){
                submit();
            });
            $('.popup #cancelBtn').on('click',function(){
                var popup = $('.popup,.popup-overlay');
                popup.fadeOut(200, function(){
                    popup.remove();
                });
            })

            $('.popup textarea').on('focus',function(){
                $('.popup #textareaTip').hide();

            })
            $('.popup textarea').on('blur',function(){
                var des = $(this).val();
                if(!des) $('.popup #textareaTip').show();
            })
            initImageUpload();
           }                  
        });
        $("#box").css("display","none");
    });

    function initImageUpload(){
        lib.puploader.image({
            browse_button: "worksUpload",
            //thumb : "w/160/h/120",
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            multi_selection:true,
            files_number:9
        },function(uploader){
            uploader.bind('FileUploaded',function(up,response){
                $('.popup #imagesUploadTip').hide();            
            });
        });                    
    }

    function submit(){
        var thumbnailsArr = $('.popup .control-thumbnails-item');
        var des = $('.popup #description').val();
        if(thumbnailsArr.length == 0) {
            $('.popup #imagesUploadTip').show();
        }
        if(!des) {
            if(!des) $('.popup #textareaTip').show();
        }
        if(thumbnailsArr.length == 0 || !des) return;
        var arr = [];
        thumbnailsArr.each(function(i,item){
            var obj = {
                "thumbimg" : $(item).find("img").attr("src"),
                "img"      : $(item).find("img").data("original")
            };
            arr.push(obj);  
        });
        lib.ajax({
            type: "post",
            url : "Works/create",
            data:{stylistId:lib.query.id,img:JSON.stringify(arr),description:des}
        }).done(function(data, status, xhr){
            if(data.result == 1){
                parent.lib.popup.result({
                    text:"操作成功！",
                    define:function(){
                        $('.popup #cancelBtn').trigger('click');
                        $(window).trigger('hashchange');    
                    }
                });                
            }                   
        }); 

    }
})()
