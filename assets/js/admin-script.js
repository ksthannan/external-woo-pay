jQuery(function($){
	$(document).ready(function(){
		
		$('#product_site').on('change', function(){
			let is_active = $(this).is(':checked');
			if(is_active){
				$('.payment_site_settings').removeClass('exwoopay_active');
				$('.product_site_settings').addClass('exwoopay_active');
				$('#payment_site').prop('checked', false);
			}else{
				$('.payment_site_settings').addClass('exwoopay_active');
				$('.product_site_settings').removeClass('exwoopay_active');
				$('#payment_site').prop('checked', true);
			}
			// console.log(is_active);
		});
		$('#payment_site').on('change', function(){
			let is_active = $(this).is(':checked');
			if(is_active){
				$('.payment_site_settings').addClass('exwoopay_active');
				$('.product_site_settings').removeClass('exwoopay_active');
				$('#product_site').prop('checked', false);
			}else{
				$('.payment_site_settings').removeClass('exwoopay_active');
				$('.product_site_settings').addClass('exwoopay_active');
				$('#product_site').prop('checked', true);
			}
			// console.log(is_active);
		});

	});
});