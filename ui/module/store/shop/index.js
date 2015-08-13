/* 
* @Author: anchen
* @Date:   2015-07-17 15:56:51
* @Last Modified by:   anchen
* @Last Modified time: 2015-08-07 10:51:07
*/

(function(){
    $("#dropMenu").on('click',function(e){
      var box=$(".area-box").fadeToggle(300);
      var icon=$(this).find('i');
      if(icon.hasClass('fa-angle-down')){
          icon.removeClass('fa-angle-down').addClass('fa-angle-up');
      }else{
          $(this).find('i').removeClass('fa-angle-up').addClass('fa-angle-down');
      }
    })
    $('body').on('click','label',function(){
        setTimeout(function(){
            $('#form').submit();            
        }, 250)
    })   
})();