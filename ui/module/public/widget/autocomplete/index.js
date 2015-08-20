$(document).on('autoinput','#search',function(e,data){
	if(data){
		$('#salon_id').val(data.salon_id);
		$('#merchant_id').val(data.merchant_id);
		$('#merchantname').val(data.merchantname);
		$('#sn').text(data.sn);
	}
});	
