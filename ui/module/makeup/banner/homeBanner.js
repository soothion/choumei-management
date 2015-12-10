/* 
* @Author: anchen
* @Date:   2015-12-03 09:50:37
* @Last Modified by:   anchen
* @Last Modified time: 2015-12-10 15:11:13
*/

$(function(){

    var moveTarget = {};

    $(".box-warpper").on('click','.plus-button button',function(){
        $(this).attr('disabled',true);
        $("form").find('button.edit').attr('disabled',true);
        $("form[id]").removeClass('move');

        var len = $('.banner').length;
        var clone = $('.template').clone();               
        clone.css('display','').removeClass("template");
        clone.find('input[type=radio]').attr('name','behavior'+len);
        clone.find('.uploader').attr('id','uploader'+len);
        clone.find("strong").text("Banner"+len); 
        var complete = clone.find('.search').attr('ajat-complete');
        complete = complete.replace('complete-position','complete-position'+len);
        clone.find('.search').attr('ajat-complete',complete);
        clone.find('.complete-position').attr('id','complete-position'+len);
        $('.plus-button').before(clone);

        new lib.Form(clone);
        if($('.banner').length >= 11 ){
            $(".plus-button").hide();
        }
        uploader('uploader'+len);        
    });

    $(".box-warpper").on('click','.del',function(){
        var self = this;
        parent.lib.popup.confirm({
            text:'确定要删除吗？',
            define:function(){                            
                var id = $(self).attr('id');
                if(id){
                    lib.ajax({
                        type: "post",
                        url : "banner/destroy/"+id
                    }).done(function(data, status, xhr){
                        if(data.result == 1){
                            parent.lib.popup.result({
                                bool : true,
                                text : "操作成功",
                                define:function(){
                                    location.reload();
                                }
                            });                 
                        }
                    })
                }else{
                    $(self).closest('.banner').remove();
                    //编辑按钮 hidden 表示 ，取消、保存按钮可见，  
                    //因为下面有个模板(form.template )的编辑按钮是hidden，所以len等于1表示所有
                    //form[draggable='false']")下的按钮都可见，可以执行下面的操作
                    var len = $("form[draggable='false']").find('button.edit.hidden').length;
                    if(len == 1){
                        $("form").find('button.edit').removeAttr('disabled');  
                        $(".plus-button button").removeAttr('disabled');
                        $("form[id]").addClass('move');                  
                    }          
                }
            }
        })

    });

    $(".box-warpper").on('click','.edit',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').removeClass('hidden');
        parent.find('.canncel').removeClass('hidden');
        parent.find('.edit').addClass('hidden');

        var topBanner = $(this).closest('.banner');
        topBanner.find('.operation').removeClass('hidden');
        topBanner.find('button.btn-primary').removeAttr('disabled');
        topBanner.find('input[type=radio]').removeAttr('disabled');
        topBanner.find('#title').removeAttr('disabled');
        var val = topBanner.find('input:checked').val();
        if(val == '1'){
            topBanner.find('#h5url').removeAttr('disabled');
        }
        if(val == '2'){
            topBanner.find('select').removeAttr('disabled')
            var selectValue = topBanner.find('select').val();   
            if(selectValue == "salon"){
                topBanner.find(".search").removeClass('hidden').removeAttr('disabled');
                topBanner.find(".salonId").removeAttr('disabled');

            }         
        }
        $("form").find('button.edit').attr('disabled',true);
        $(".plus-button button").attr('disabled',true);
        $("form[id]").removeClass('move');  
    });

    $(".box-warpper").on('click','.canncel',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').addClass('hidden');
        parent.find('.canncel').addClass('hidden');
        parent.find('.edit').removeClass('hidden');
        var topBanner = $(this).closest('.banner');
        var url = topBanner.attr('url');
        if(url){
            if(topBanner.find('.thumbnails-item-img').length > 0){
                topBanner.find('.thumbnails-item-img img').attr('src',url);
                topBanner.find('.thumbnails-item-img input').attr('value',url);                
            }else{
                topBanner.find('.thumbnails-item-btn').css('display','none');
                appendImgItem(topBanner.find('.thumbnails-item'),url);                
            }
        }else{
            topBanner.find('.thumbnails-item-btn').css('display','inline-block');
            topBanner.find('.thumbnails-item-img').remove();
        }
        $("form[id]").addClass('move');  
        topBanner.find('.operation').addClass('hidden');
        topBanner.find('.control-help').hide();
        topBanner.find('input[type=radio]').attr('disabled',true);
        topBanner.find('#title').attr('disabled',true);
        topBanner.find('#h5url').attr('disabled',true);
        topBanner.find('select').attr('disabled',true);
        topBanner.find('.search').attr('disabled',true);
        topBanner.find('.salonId').attr('disabled',true);
        topBanner.find('button.btn-primary').attr('disabled',true);
        $("form").find('button.edit').removeAttr('disabled');
        $(".plus-button button").removeAttr('disabled');    
    });

    $(".box-warpper").on('click','input[type=radio]',function(){ 
        var box = $(this).closest('.inputBox');
        box.find('#h5url').attr('disabled',true);
        box.find('select').attr('disabled',true);
        box.find('.search').attr('disabled',true); 
        box.find('.salonId').attr('disabled',true); 
        box.find('.control-help').hide();
        var radios = $(this).closest('.radios');
        radios.find('input[disabled]').removeAttr('disabled'); 
        radios.find('select[disabled]').removeAttr('disabled');        
    });

    $(".box-warpper").on('_ready',function(e){
        if(!$(e.target).hasClass('box-warpper')) return;
        var uploadBtnArr = $(this).find('button[id^=uploader]');
        uploadBtnArr.each(function(i,item){
            uploader($(item).attr('id'));
        });

        lib.ajax({
            type: "get",
            url : 'beautyItem/itemList',
            async : true,
        }).done(function(data, status, xhr){
            if(data.result==1 && data.data){
                var arr = [];
                arr.push("<option value='salon'>美发店铺主页</option>");
                arr.push("<option value='artificers'>专家主页</option>");
                var obj = {'1':'SPM','2':'FFA'}
                data.data.forEach(function(item,i){
                    arr.push("<option value='"+obj[item.type]+"_"+item.item_id+"'>"+item.name+"</option>");
                });
                $("select").each(function(i,item){
                    $(item).append(arr.join(''));
                    if($(item).attr('url')){
                        var url = JSON.parse($(item).attr('url'));
                        if(url.type=="salon"){
                            $($(item).find('option')[0]).attr('selected',true)
                        }else if(url.type=="artificers"){
                            $($(item).find('option')[1]).attr('selected',true)
                        }else{
                            $(item).find('option[value='+url.type+'_'+url.itemId+']').attr('selected',true)
                        }
                    }
                })
            }
        })         
    });

    $(".box-warpper").on('change','select',function(e){
        $(this).find('option[selected=selected]').removeAttr('selected');
        $(this).find('option[value='+$(this).val()+']').attr('selected','selected');
        if($(this).val()=="salon"){
            $(this).next().find('input').removeClass('hidden').removeAttr('disabled');
        }else{
            $(this).next().find('input').addClass('hidden').attr('disabled',true);
        }
    });

    $(".box-warpper").on('autoinput','.search',function(e,data){
        var banner = $(this).closest('.banner');
        if(data){
            banner.find('.salonId').val(data.salon_id);
        }
    }).on('input','.search',function(){
        var banner = $(this).closest('.banner')
        banner.find('.salonId').val("");
    });     
    
    $(".box-warpper").on('dragover','form[id]',function(ev){
        ev.preventDefault();
    });

    $(".box-warpper").on('drop','form[id]',function(ev){
        ev.preventDefault();
        if($(ev.currentTarget).attr('id') == moveTarget.attr('id')){
            return;
        }        
        var targetId = $(ev.currentTarget).attr('id');
        var prevId = moveTarget.prev().attr('id');
        if(targetId==prevId){
            $(ev.currentTarget).before(moveTarget.clone());
        }else{
            $(ev.currentTarget).after(moveTarget.clone());           
        }
        moveTarget.remove();
        var arr = [];
        $('form[id]').each(function(i,item){
            arr.push({id:$(item).attr('id'),sort:i+1});
        });

        lib.ajax({
            type: "post",
            url : 'banner/sort',
            data: {sort:JSON.stringify(arr)}
        }).complete(function(xhr, status){
            var data = JSON.parse(xhr.responseText);
            if(data.result == 0){
                parent.lib.popup.result({
                    bool : false,
                    text : data.msg || "移动排序失败",
                    define:function(){
                        location.reload();
                    }
                });                 
            }
        });                    
    }); 

    $(".box-warpper").on('dragstart','form[id]',function(ev){       
        moveTarget = $(ev.currentTarget);    
        if($('.box-warpper').find('form[url]').find('button.edit[disabled]').length>0){
            return false;
        }
    });

    lib.Form.prototype.save = function(data){
        if(!data.image){
            $(this.el).find('.imageTip').show();
            return;
        }
        data.behavior = $(this.el).find('input:checked').val();
        if(data.behavior=="2"){
            var arr = data.url.split("_");
            if(arr[0]=="salon"){
                data.url = {type:arr[0]};
            }else if(arr[0]=="artificers"){ 
                data.url = {type:arr[0]};
            }else{
                data.url = {type:arr[0],itemId:arr[1]};
            }
            data.url = JSON.stringify(data.url);
        }     
        lib.ajax({
            type: "post",
            url : data.id ? "banner/edit/"+data.id : "banner/create",
            data:data
        }).done(function(data, status, xhr){
            if(data.result == 1){
                parent.lib.popup.result({
                    text:"操作成功！",
                    define:function(){
                        location.reload();
                    }
                });                  
            }
        })         
    }

    function uploader(id){
        lib.puploader.image({
            browse_button: id,
            auto_start:true,
            filters: {
            mime_types : [
            { title : "Image files", extensions : "jpg,png,jpeg,gif" },
            ]
            },
            max_file_size:'10mb',
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            var item = $('#'+id).closest('.thumbnails-item');
            uploader.bind('ImageUploaded',function(up,response){
                var ratio = new Number(750/528).toFixed(2);
                createThumbnails(up,response,ratio,'750x528',item);
            });
            item.on('click','.fa-pencil-square-o',function(){                
                var src = item.find('img').attr('src');
                src = src.split('?')[0]+"?imageView2/0/w/720/h/1280";
                uploader.trigger('ImageUploaded',{img:src});
            })
            item.on('click','.fa-times-circle-o',function(){                
                item.find('.thumbnails-item-img').remove();
                item.find('.thumbnails-item-btn').show();
            })  
        });
    } 

    function createThumbnails(up,response,ratio,name,item){
        lib.cropper.create({
            src:response.img,
            aspectRatio : ratio,
            thumbnails  : [name],
            define:function(data){
                item.closest('.banner').find(".imageTip").hide();
                item.find('.thumbnails-item-btn').css('display','none');
                item.find('.thumbnails-item-img').remove();
                item.removeClass('dashed');
                appendImgItem(item,data[name]);
            }
        });        
    }

    function appendImgItem(item,url){
        item.append('<div class="thumbnails-item-img"><img src='+url+'><input type="hidden" name="image" value='+url+' required><div class="operation"><i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;<i class="fa fa-times-circle-o"></i></div><div>');        
    }                  
});