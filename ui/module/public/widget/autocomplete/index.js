$(document).on('autoinput','#search',function(e,data){
	if(data){
		$('#merchantId').val(data.merchant_id);
		$('#merchantname').val(data.merchantname);
		$('#sn').text(data.sn);
		$('#salonid').val(data.salon_id);
	}
});	
