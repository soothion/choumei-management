/* 
* @Author: anchen
* @Date:   2015-12-03 09:50:37
* @Last Modified by:   anchen
* @Last Modified time: 2015-12-05 18:15:27
*/

$(function(){

    var moveTarget = {};

    $(".box-warpper").on('click','.plus-button',function(){
        var len = $('.banner').length;
        var clone = $('.template').clone();               
        clone.css('display','').removeClass("template");
        clone.find('input[type=radio]').attr('name','behavior'+len);
        clone.find('.uploader').attr('id','uploader'+len);
        clone.find("strong").text("Banner"+len);     
        $(this).before(clone);
        new lib.Form(clone);
        if($('.banner').length >= 11 ){
            $(".plus-button").hide();
        }
        uploader('uploader'+len);        
    });

    $(".box-warpper").on('click','.del',function(){
        var id = $(this).attr('id');
        if(id){
            lib.ajax({
                type: "post",
                url : "banner/destroy/"+id
            }).done(function(data, status, xhr){
                if(data.result == 1){
                    location.reload();
                }
            })
        }else{
            $(this).closest('.banner').remove();
        }
    });

    $(".box-warpper").on('click','.edit',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').removeClass('hidden');
        parent.find('.canncel').removeClass('hidden');
        parent.find('.edit').addClass('hidden');

        var topBanner = $(this).closest('.banner');
        topBanner.removeClass('move');
        topBanner.siblings().find('button.edit').attr('disabled',true);
        topBanner.find('.operation').removeClass('hidden');

        topBanner.find('input[type=radio]').removeAttr('disabled');
        topBanner.find('#title').removeAttr('disabled');

        var val = topBanner.find('input:checked').val();
        if(val == '1'){
            topBanner.find('#h5url').removeAttr('disabled');
        }
        if(val == '2'){
            topBanner.find('select').removeAttr('disabled')
            var selectValue = topBanner.find('select').val();   
            if(selectValue == "salons_salonId"){
                topBanner.find("#search").removeClass('hidden').removeAttr('disabled'); 
            }         
        }
    });

    $(".box-warpper").on('click','.canncel',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').addClass('hidden');
        parent.find('.canncel').addClass('hidden');
        parent.find('.edit').removeClass('hidden');
        var topBanner = $(this).closest('.banner');
        var url = topBanner.attr('url');
        if(url){
            topBanner.find('.thumbnails-item-img img').attr('src',url);
            topBanner.find('.thumbnails-item-img input').attr('value',url);
        }else{
            topBanner.find('.thumbnails-item-btn').css('display','inline-block');
            topBanner.find('.thumbnails-item-img').remove();
        }
        topBanner.addClass('move');
        topBanner.find('.operation').addClass('hidden');
        topBanner.find('.control-help').hide();
        topBanner.siblings().find('button.edit').removeAttr('disabled');
        topBanner.find('input[type=radio]').attr('disabled',true);
        topBanner.find('#title').attr('disabled',true);
        topBanner.find('#h5url').attr('disabled',true);
        topBanner.find('select').attr('disabled',true);
        topBanner.find('#search').attr('disabled',true);    
    });

    $(".box-warpper").on('click','input[type=radio]',function(){ 
        var box = $(this).closest('.inputBox');
        box.find('#h5url').attr('disabled',true);
        box.find('select').attr('disabled',true);
        box.find('#search').attr('disabled',true); 
        box.find('.control-help').hide();
        var radios = $(this).closest('.radios');
        radios.find('input[disabled]').removeAttr('disabled'); 
        radios.find('select[disabled]').removeAttr('disabled');        
    });

    $(".box-warpper").on('_ready',function(){
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
                arr.push("<option value='salons_salonId'>美发店铺主页</option>");
                arr.push("<option value='artificers'>专家主页</option>");
                var obj = {'1':'SPM','2':'FFA'}
                data.data.forEach(function(item,i){
                    arr.push("<option value='"+obj[item.type]+"_"+item.item_id+"'>"+item.name+"</option>");
                });
                $("select").each(function(i,item){
                    $(item).append(arr.join(''));
                    if($(item).attr('url')){
                        var url = JSON.parse($(item).attr('url'));
                        if(url.type=="salons"){
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
        if($(this).val()=="salons_salonId"){
            $(this).next().removeAttr('disabled');
            $(this).next().removeClass('hidden');
        }else{
             $(this).next().attr('disabled',true);
            $(this).next().addClass('hidden');
        }
    });
    
    $(".box-warpper").on('dragover',function(ev){
        ev.preventDefault();
    });

    $(".box-warpper").on('drop','form',function(ev){
        ev.preventDefault();
        if($(ev.currentTarget).attr('id') == moveTarget.attr('id')){
            return;
        }
        $(ev.currentTarget).after(moveTarget.clone());
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
                    text : data.msg || "操作失败",
                    define:function(){
                        location.reload();
                    }
                });                 
            }
        });                    
    }); 

    $(".box-warpper").on('dragstart','form',function(ev){       
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
            if(arr[0]=="salons"){
                data.url = {type:arr[0],salonId:'salonId'};
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
            item.on('click','.fa',function(){                
                var src = item.find('img').attr('src');
                src = src.split('?')[0]+"?imageView2/0/w/720/h/1280";
                uploader.trigger('ImageUploaded',{img:src});
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
                item.append('<div class="thumbnails-item-img"><img src='+data[name]+'><input type="hidden" name="image" value='+data[name]+' required><div class="operation"><i class="fa fa-pencil-square-o"></i></div><div>');
            }
        });        
    }                  
});