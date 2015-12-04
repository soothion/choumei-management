/* 
* @Author: anchen
* @Date:   2015-12-03 09:50:37
* @Last Modified by:   anchen
* @Last Modified time: 2015-12-03 19:43:55
*/

$(function(){
    $(".box-warpper").on('click','.plus-button',function(){
        var bannerHTML = $('.template')[0].outerHTML;
        var id = 'uploader'+$('.banner').length;
        var clone = $(bannerHTML);
        clone.css('display','inline-block');
        clone.removeClass("template");
        clone.find('.uploader').attr('id',id);
        clone.find("strong").text("Banner"+$('.banner').length);  
        $(this).before(clone);
        if($('.banner').length >= 11 ){
            $(".plus-button").hide();
        }
        uploader(id);        
    })

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
    })

    $(".box-warpper").on('click','.edit',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').removeClass('hidden');
        parent.find('.canncel').removeClass('hidden');
        parent.find('.edit').addClass('hidden');

        var topBanner = $(this).closest('.banner');
        topBanner.find('.operation').removeClass('hidden');
        topBanner.find('input').removeAttr('disabled');
        topBanner.find('select').removeAttr('disabled');
    })

    $(".box-warpper").on('click','input[type=radio]',function(){
        var top = $(this).closest('li');
        top.find('.radios').find('input').attr('disabled',true);
        top.find('.radios').find('input').addClass('dis-line');
        top.find('.radios').find('select').attr('disabled',true);
        top.find('.radios').find('select').addClass('dis-line');

        var parent = $(this).closest('.radios');
        parent.find('input[readOnly]').removeAttr('disabled'); 
        parent.find('select[disabled]').removeAttr('disabled');
    })

    $(".box-warpper").on('click','.canncel',function(){
        var parent = $(this).closest('.fr');
        parent.find('.save').addClass('hidden');
        parent.find('.canncel').addClass('hidden');
        parent.find('.edit').removeClass('hidden');

        var topBanner = $(this).closest('.banner');
        topBanner.find('.operation').addClass('hidden');
        topBanner.find('input').attr('disabled',true);
        topBanner.find('select').attr('disabled',true);
    })

    // $(".box-warpper").on('click','.save',function(){
    //     var topBanner = $(this).closest('.banner');
    //     var data = {};
    //     var imgUrl= {}
    //     var image = topBanner.find('img');
    //     imgUrl.img = image.attr('src');
    //     imgUrl.thumbimg = image.attr('original');
    //     data.image = JSON.stringify(imgUrl);
    //     data.name  = topBanner.find("input[name='name']").val();
    //     var radio  = topBanner.find('input:checked');
    //     var parent = radio.colsest('radios');
    //     data.behavior = radio.val();
    //     if(data.behavior == "1"){
    //         data.url = parent.find('input[text]').val();
    //     }
    //     if(data.behavior == "2"){
    //         var arr = parent.find('select').val().split("_");
    //         data.url = {type:arr[0],itemId:arr[1]};
    //         data.url = JSON.stringify(data.url);
    //     }
    // })

    lib.Form.prototype.save = function(data){
  
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
            uploader.bind('ImageUploaded',function(up,response){
                var ratio = new Number(750/500).toFixed(2);
                createThumbnails(up,response,750/500,'750x500',id);
            });    
        });
    } 


    function createThumbnails(up,response,ratio,name,id){
        lib.cropper.create({
            src:response.img,
            aspectRatio : ratio,
            thumbnails  : [name],
            define:function(data){
                var thumbnailsItem = $('#'+id).parent();
                thumbnailsItem.removeClass('dashed');
                thumbnailsItem.empty();
                thumbnailsItem.append('<img src='+data[name]+' data-original='+response.img+'><div class="operation"><i class="fa fa-pencil-square-o"></i></div>'); 
            }
        });        
    }                  
});