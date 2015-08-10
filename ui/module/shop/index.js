/* 
* @Author: anchen
* @Date:   2015-07-17 15:56:51
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-17 17:06:27
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

    $('#select').on('change',function(){
      var $this=$(this);
      $this.parent().find('input').eq($this.val()).show().siblings('input').hide().val('');
    });

    $('body').on('click','label',function(){
        setTimeout(function(){
            $('#form').submit();            
        }, 250)
    })   
})();