/* 
* @Author: anchen
* @Date:   2015-09-28 11:17:09
* @Last Modified by:   anchen
* @Last Modified time: 2015-10-23 14:46:40
*/

(function(){
    var type = lib.query.type;
    var currentData = {};

    var init = function(){
        initPage();
        initEvent();
    }

    var initPage = function(){
        //新增
        if(type === 'add'){
            currentData = JSON.parse(sessionStorage.getItem('add-shop-data')); 
            document.body.onbeforeunload=function(){return "确定离开当前页面吗？";}
        }
        //编辑
        if(type === 'edit'){
            currentData = JSON.parse(sessionStorage.getItem('edit-shop-data')); 
        }

        if(currentData.salonLogo && typeof currentData.salonLogo == 'string'){
            currentData.salonLogo = JSON.parse(currentData.salonLogo);
        }

        if(currentData.salonImg && typeof currentData.salonImg == 'string'){
            currentData.salonImg = JSON.parse(currentData.salonImg);
        }

        if(currentData.workImg && typeof currentData.workImg == 'string'){
            currentData.workImg = JSON.parse(currentData.workImg);
        } 
        initUploader();       
    }

    var initUploader = function(){
        lib.puploader.image({
            browse_button: 'logoUpload',
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            imageArray:currentData.salonLogo,
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                createThumbnails(up,response,1,'300x300');                
            });

            uploader.thumbnails.bind('itemchange',function(){
                saveImagesUrl();
            });
        });

        lib.puploader.image({
            browse_button: 'shopUpload',
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            imageArray:currentData.salonImg,
            multi_selection:true,
            files_number:4,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                var ratio = new Number(1125/405).toFixed(2);
                createThumbnails(up,response,ratio,'1125x405');                
            });

            uploader.thumbnails.bind('itemchange',function(){
                saveImagesUrl();
            });
        });

        lib.puploader.image({
            browse_button: 'teamUpload',
            auto_start:true,
            filters: {
                mime_types : [
                    { title : "Image files", extensions : "jpg,png,jpeg,gif" },
                ]
            },
            max_file_size:'10mb',
            imageArray:currentData.workImg,
            multi_selection:true,
            files_number:1,
            crop:true
        },function(uploader){
            uploader.bind('ImageUploaded',function(up,response){
                var ratio = new Number(1125/405).toFixed(2);
                createThumbnails(up,response,ratio,'1125x405');                
            });

            uploader.thumbnails.bind('itemchange',function(){
                saveImagesUrl();
            });
        });                 
    }

    var createThumbnails = function(up,response,ratio,name){
        lib.cropper.create({
            src:response.img,
            aspectRatio : ratio,
            thumbnails  : [name],
            define:function(data){
                if(up.createThumbnails&&!response.edit){
                    up.createThumbnails({
                        thumbimg:data[name],
                        img:response.img,
                        ratio:ratio,
                        type : 1
                    },function(){
                        saveImagesUrl();                               
                    });
                }
            }
        });        
    }

    var saveImagesUrl=function(){
        currentData.salonLogo = [];
        $('#control-thumbnails1 .control-thumbnails-item').each(function(){
            var $this=$(this);
            currentData.salonLogo.push({
                thumbimg:$this.find('input[name="thumb"]').val(),
                img:$this.find('input[name="original"]').val(),
                ratio : $this.find('input[name="ratio"]').val(),
                type  : 1
            });
        });
        currentData.salonImg = [];
        $('#control-thumbnails2 .control-thumbnails-item').each(function(){
            var $this=$(this);
            currentData.salonImg.push({
                thumbimg:$this.find('input[name="thumb"]').val(),
                img:$this.find('input[name="original"]').val(),
                ratio : $this.find('input[name="ratio"]').val(),
                type  : 1                
            });
        });
        currentData.workImg = [];
        $('#control-thumbnails3 .control-thumbnails-item').each(function(){
            var $this=$(this);
            currentData.workImg.push({
                thumbimg:$this.find('input[name="thumb"]').val(),
                img:$this.find('input[name="original"]').val(),
                ratio : $this.find('input[name="ratio"]').val(),
                type  : 1                
            });
        });
        if(type === 'add')  sessionStorage.setItem('add-shop-data',JSON.stringify(currentData));
        if(type === 'edit') sessionStorage.setItem('edit-shop-data',JSON.stringify(currentData));        
    }

    var initEvent = function(){
        // $(".control-thumbnails").on('click','.control-thumbnails-edit',function(){
        //     var item  = $(this).closest('.control-thumbnails-item');
        //     var ratio = item.find('img').data('ratio'); 
        //     lib.cropper.create({
        //         src:item.find('img').data('original'),
        //         aspectRatio : item.find('img').data('ratio'),
        //         thumbnails  : ratio == "1" ? ['300x300'] : ['1125x405'], 
        //         define:function(data){
        //             item.find('img').attr('src',data[ratio == "1" ? '300x300':'1125x405']);
        //             item.find('input.thumb').val(data[ratio == "1" ? '300x300':'1125x405']);
        //             saveImagesUrl();  
        //         }                     
        //     });
        // });

        // $(".control-thumbnails").on('click','.control-thumbnails-before',function(){
        //     var $this=$(this);
        //     var thumbnail=$this.closest('.control-thumbnails-item');
        //     var prev=thumbnail.prev('.control-thumbnails-item')
        //     if(prev.length==1){
        //         thumbnail.after(prev);
        //         saveImagesUrl();  
        //     }
        // });

        // $(".control-thumbnails").on('click','.control-thumbnails-after',function(){
        //     var $this=$(this);
        //     var thumbnail=$this.closest('.control-thumbnails-item');
        //     var next=thumbnail.next('.control-thumbnails-item')
        //     if(next.length==1){
        //         thumbnail.before(next);
        //         saveImagesUrl();  
        //     }
        // });

        $(".flex-item a").on('click',function(e){
            e.preventDefault();
            location.href = $(this).attr('href') + "?type="+type;
        });

        $(".preview").on('click',function(){
            sessionStorage.setItem("preview-shop-data",JSON.stringify(currentData));
            window.open("detail.html?type=preview");
        })                        

        $(".submit").on('click',function(){
            document.body.onbeforeunload=function(){}
            location.href="bank.html?type="+type;
        });  
    }

    init();
})();