/* 
* @Author: anchen
* @Date:   2015-07-07 09:42:01
* @Last Modified by:   anchen
* @Last Modified time: 2015-07-07 10:26:02
*/

(function(){
    var type = utils.getSearchString("type");
    $(".flex-item a").on('click',function(e){
        e.preventDefault();
        location.href = $(this).attr('href') + "?type="+type;
    });    
})();