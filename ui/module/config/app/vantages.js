/* 
* @Author: anchen
* @Date:   2015-10-16 14:28:21
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-16 20:10:56
*/

(function(){

    $(".wrapper").on('click','a.add',function(){
        var self = this;
        parent.lib.popup.box({
            title:'增加积分',
            height:350,
            width:600,
            content:$("#box").html(),
            complete:function(){
                bindEvent();
                $(".popup").on('click','.btn-primary',function(){
                    submit(1,$(self).data('salonid'));
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
            content:$("#box").html(),
            complete:function(){
                bindEvent();
                $(".popup").on('click','.btn-primary',function(){
                    submit(2,$(self).data('salonid'));
                }) 
            }
        });                        
    });

    function bindEvent(){
        $(".popup").on('blur','input',function(){
            if($(this).val()){
                if($(this).attr("score")){
                   if($(this).val()*1 > $(this).attr('score')*1){
                        $(this).parent().find(".control-help").text("扣减积分必须小于等于店铺累计积分");
                        $(this).parent().find(".control-help").addClass('show');                              
                    }else{
                        $(this).parent().find(".control-help").removeClass('show');
                    }
                }else{
                    $(this).parent().find(".control-help").removeClass('show');
                }  
            }else{
                $(this).parent().find(".control-help").text("未填写");
                $(this).parent().find(".control-help").addClass('show');                        
            }
      
        });

        $(".popup").on('blur','textarea',function(){
            if($(this).val()){
                $(this).parent().find(".control-help").removeClass('show');                        
            }else{
                $(this).parent().find(".control-help").text("未填写");
                $(this).parent().find(".control-help").addClass('show');
            }
        });

        $(".popup").on('click','.btn',function(){
            $(".popup-overlay").remove();
            $(".popup").remove();
        })

        $(".popup").on('keydown',"input[pattern='number']",function(e){
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
    }


    function submit(type,salonid){
        $(".popup").find('input').trigger('blur');
        $(".popup").find('textarea').trigger('blur');
        if($(".popup").find("span.show").length>0) return;
        var score = $(".popup").find('input').val();
        var msg =  $(".popup").find('textarea').val();

        lib.ajax({
            type: "post",
            data : {'type':type,'salonid':salonid,'score':score,'msg':msg},
            url : "salonstar/update"
        }).done(function(data, status, xhr){
            if(data.result == 1){
                $(".popup .btn").trigger('click');
                $(window).trigger('hashchange');
            }
        });                
    } 
})();