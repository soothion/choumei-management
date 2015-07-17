/* 
* @Author: anchen
* @Date:   2015-07-17 15:56:51
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-17 17:06:27
*/

(function(){
    $("#dropMenu").on('click',function(e){
      $(".area-box").fadeToggle(300);
    })

    $('#select').on('change',function(){
      var $this=$(this);
      var input1=$('input[name="salonname"]');
      var input2=$('input[name="businessName"]');
      if($this.val()==1){
        input1.show();
        input2.hide().val('');
      }else{
        input2.show();
        input1.hide().val('');
      }
    });

    $('body').on('click','label',function(){
        setTimeout(function(){
            $('#form').submit();            
        }, 250)
    })   
})();