/* 
* @Author: anchen
* @Date:   2015-09-21 17:44:57
* @Last Modified by:   anchen
* @Last Modified time: 2015-09-23 11:08:11
*/

  $(document).ready(function(){
      $("#table").delegate('tbody input[type="checkbox"]', 'change', function(event) {
          var button=$('.table-bottom button');
          if($('tbody label input:checked').length>0){
            button.attr('disabled',false);
          }else{
            button.attr('disabled',true);
          }
      });

      $("#table").delegate(".reject",'click',function(){
          var type = $(this).data('type');
          var arr     = [];          
          if(type == "0") arr.push($(this).data('id'));
          if(type == "1") {
            $('tbody input[type="checkbox"]:checked').each(function(index,obj){
              arr.push($(obj).data('id'));
            })
          }

          parent.lib.popup.prompt({
             text   : '拒绝原因： ',
             define : function(str){
                lib.ajax({
                  url  : 'refund/reject',
                  type : "post",
                  data :  {ids:arr.join(","),reason:str}
                }).done(function(data, status, xhr){
                  parent.lib.popup.result({
                    bool:data.result == 1,
                    text:(data.result == 1 ? "操作成功" : data.msg),
                    time:2000,
                    define:function(){
                      lib.ajat('refund/index?<%=query._%>#domid=table&tempid=table-t').render();
                    }
                  });
                });  
             }
          });        
      });

     $("#table").delegate('.pass', 'click', function(event) {
          var type = $(this).data('type');
          var arr = [];
          var message = "你确定要执行同意操作？";
          if(type == "0") {
            arr.push($(this).data('id'));
          }
          if(type == "1") {
            arr.push($(this).data('id'));
            message = "你确定要执行重新退款操作？";
          }
          if(type == "2") {
            $('tbody input[type="checkbox"]:checked').each(function(index,obj){
              arr.push($(obj).data('id'));
            })
          }         
          parent.lib.popup.confirm({
              text:message,
              define:function(){
                lib.ajax({
                  url  : 'refund/accept',
                  type : "post",
                  data :  {ids:arr.join(",")}
                }).done(function(data, status, xhr){
                  if(data.result == "0"){
                    parent.lib.popup.result({bool:false,text:data.msg,time:2000});
                    return;
                  }

                  if(data.result == "1"){

                    data = data.data;

                    if(data.alipay && data.alipay.form_args){
                      $.each($("#alipaysubmit").serializeArray(),function(i,field){
                        $("input[name='"+field.name+"']").val(data.alipay.form_args[field.name])
                      })
                      $("#alipaysubmit").submit();
                      return;
                    }

                    if(data.alipay && data.alipay.info){
                        parent.lib.popup.result({bool:true,text:data.alipay.info,time:2000});
                    }

                    if(data.wx && data.wx.info){
                        parent.lib.popup.result({bool:true,text:data.wx.info,time:2000});
                    }

                    if(data.yilian && data.yilian.info){
                        parent.lib.popup.result({bool:true,text:data.yilian.info,time:2000});
                    }

                    if(data.balance && data.balance.info){
                        parent.lib.popup.result({bool:true,text:data.yilian.info,time:2000});
                    }
                    lib.ajat('refund/index?<%=query._%>#domid=table&tempid=table-t').render();
                  }                                   
                });                        
              }
          });          
     });

     function request(url,params){
        lib.ajax({
          url  :  url,
          type : "post",
          data :  params
        }).done(function(data, status, xhr){
          parent.lib.popup.result({
            bool:data.result == 1,
            text:(data.result == 1 ? "操作成功" : data.msg),
            time:2000,
            define:function(){
              lib.ajat('refund/index?<%=query._%>#domid=table&tempid=table-t').render();
            }
          });
        });       
     }
  });