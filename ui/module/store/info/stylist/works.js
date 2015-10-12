/* 
* @Author: anchen
* @Date:   2015-10-12 13:59:43
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-12 15:10:07
*/

(function(){
   $("#works-wrapper").on("click",".control-thumbnails-remove",function(){
        var self = this;
        parent.lib.popup.confirm({
            text:"正在删除此图片，是否继续?",
            define:function(){
                var id  = $(self).data("id");  
                var sib = $(self).parent().siblings();
                var str = "";
                var arr = []; 
                sib.each(function(i,item){
                    var obj = {
                        "thumbimg" : $(item).find("img").attr("src"),
                        "img"      : $(item).find("img").attr("img")
                    };
                    arr.push(obj);                        
                })
                if(arr.length > 0){
                    str = JSON.parse(arr);
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
       thumbnail.find(img).each(function(i,item){
            var obj = {
                "thumbimg" : $(item).find("img").attr("src"),
                "img"      : $(item).find("img").attr("img")
            };
            arr.push(obj);                        
        })                    
          
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
            initImageUpload();
           }                  
        });
        $("#box").css("display","none");
    });

    function submit(){
        var arr = $('.popup .control-thumbnails-item');
        var des = $('.popup #description').val();
        if(arr.length == 0) return;
        if(!des) return;
        arr.each(function(i,item){
            var obj = {
                "thumbimg" : $(item).find("img").attr("src"),
                "img"      : $(item).find("img").attr("img")
            };
            arr.push(obj);  
        });
        lib.ajax({
            type: "post",
            url : "Works/create",
            data:{stylistId:"",img:JSON.stringify(arr),description:des}
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

    function initImageUpload(){
        lib.puploader.image({
            browse_button: "worksUpload",
            thumCss : "add",
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            multi_selection:true,
            files_number:10
        });                    
    }
})()
