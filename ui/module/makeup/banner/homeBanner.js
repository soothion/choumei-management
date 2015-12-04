/* 
* @Author: anchen
* @Date:   2015-12-03 09:50:37
* @Last Modified by:   anchen
* @Last Modified time: 2015-12-04 18:13:04
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
        clone.attr('id','form'+len);       
        $(this).before(clone);
        new lib.Form(clone);
        if($('.banner').length >= 11 ){
            $(".plus-button").hide();
        }
        uploader('uploader'+len);        
    });

    $(".box-warpper").on('click','.del',function(){
        var id = $(this).attr('id');
        lib.ajax({
            type: "post",
            url : "banner/destroy/"+id
        }).done(function(data, status, xhr){
            if(data.result == 1){
                location.reload();
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
        topBanner.find('input').removeClass('hidden').removeClass('background'); 
        topBanner.find('select').removeClass('hidden').removeClass('background');
        
        topBanner.find('input[type=radio]').removeAttr('disabled');  
        topBanner.find('input[name=name]').removeAttr('disabled');
        topBanner.removeClass('move');  
        var li = topBanner.find('input:checked').closest('.radios');       
        li.find('input').removeAttr('disabled');
        li.find('select').removeAttr('disabled');        
    });

    $(".box-warpper").on('click','input[type=radio]',function(){
        var top = $(this).closest('li');
        top.find('.radios').find('input[type=text]').attr('disabled',true);
        top.find('.radios').find('select').attr('disabled',true);

        var parent = $(this).closest('.radios');
        parent.find('input[disabled]').removeAttr('disabled'); 
        parent.find('select[disabled]').removeAttr('disabled');
    });

    $(".box-warpper").on('click','.canncel',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').addClass('hidden');
        parent.find('.canncel').addClass('hidden');
        parent.find('.edit').removeClass('hidden');

        var topBanner = $(this).closest('.banner');
        topBanner.addClass('move');
        topBanner.find('.operation').addClass('hidden');
        topBanner.find('input[type=text]').attr('disabled',true)
        .addClass('background').addClass('hidden');
        topBanner.find('select').attr('disabled',true)
        .addClass('background').addClass('hidden');
        topBanner.find('input[name=name]').removeClass('hidden');
        var li = topBanner.find('input:checked').closest('.radios');
        li.find('input').removeClass('hidden');
        li.find('select').removeClass('hidden');


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
    
    $(".box-warpper").on('dragover',function(ev){
        ev.preventDefault();
    });

    $(".box-warpper").on('drop','form',function(ev){
        ev.preventDefault();
        if($(ev.currentTarget).attr('id') == moveTarget.attr('id')){
            return;
        }
        var clone = moveTarget.clone();
        moveTarget.remove();
        $(ev.target).closest('form').after(clone);                     
    });

    $(".box-warpper").on('dragstart','form',function(ev){       
        moveTarget = $(ev.currentTarget);
        if(moveTarget.find('.edit').hasClass('hidden')){
            return false;
        }
    });


    lib.Form.prototype.save = function(data){
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
                item.find('.thumbnails-item-btn').css('display','none');
                item.find('.thumbnails-item-img').remove();
                item.removeClass('dashed');
                item.append('<div class="thumbnails-item-img"><img src='+data[name]+'><input type="hidden" name="image" value='+data[name]+' required><div class="operation"><i class="fa fa-pencil-square-o"></i></div><div>');
            }
        });        
    }                  
});