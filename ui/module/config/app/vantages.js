/* 
* @Author: anchen
* @Date:   2015-10-16 14:28:21
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-16 20:10:56
*/
(function(){
    $(".wrapper").on('click','a.add',function(){
        var self = this;
		var ln=location;
        parent.lib.popup.box({
            title:'增加积分',
            height:350,
            width:600,
            content:lib.ejs.render({text:$("#box").html()},{data:{type:1,salonid:$(this).data('salonid')}}),
            complete:function(){
			   var popup=$(this);
               var $form=popup.find('form');
			   new lib.Form($form[0]);
			   $form[0].goback=function(){}
			   $form.off("success").on("success",function(){
					parent.lib.popup.result({
						define:function(){
							ln.reload();
						}
					});
			   });
			   popup.find(".btn").on('click',function(){
				   parent.lib.popup.close();
			   })
            }
        });                        
    });
    $(".wrapper").on('click','a.minus',function(){
        $("#box").find("#labelText").text("减少积分");
        $("#box").find('input').attr("score",$(this).data('score'));
        var self = this;
        parent.lib.popup.box({
            title:'减少积分',
            height:350,
            width:600,
            content:lib.ejs.render({text:$("#box").html()},{data:{type:2,salonid:$(this).data('salonid')}}),
            complete:function(){
			   var popup=$(this);
               var $form=popup.find('form');
			   new lib.Form($form[0]);
			   $form[0].goback=function(){}
			   $form.off("success").on("success",function(){
					parent.lib.popup.result({
						define:function(){
							ln.reload();
						}
					});
			   });
			   popup.find(".btn").on('click',function(){
				   parent.lib.popup.close();
			   })
            }
        });                        
    });
})();