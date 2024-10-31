jQuery( function ( $ ) {

	$('.ajax-loader').addClass('hide');
	$("#order_id").keydown(function(e)
	{
		if(e.keyCode==13) //as barcode scanner do
		{
			e.preventDefault();
			var orderid=$("#order_id").val();
			var data = {action: 'getOrderDetails',orderid:orderid};
			$.post(woobarsettings.ajaxurl,data,function(res)
			{
				$(".result").html(res);			
			});		
		}
		
	});
	$( document ).ajaxStart(function() {
	  $('.ajax-loader').removeClass('hide');
	});
	$( document ).ajaxStop(function() {
	  $('.ajax-loader').addClass('hide');
	});
	$( document ).ajaxError(function() 
	{
		$('.ajax-loader').removeClass('hide');
	});
	
});