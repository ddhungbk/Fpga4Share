(function(){
	jQuery(document).ready(function($) {
		var handleWatermark = function(input){
			if(input.val().trim() != "") {
				input.removeClass('os-watermark');
			} else {
				input.val(input.data('watermark'));
				input.addClass('os-watermark');
			}
		};		
		$('.opinionstage-wrap input.watermark').focus(function(){
			var input = $(this);
			if (input.data('watermark') == input.val()) {
				input.val("");
				input.removeClass('os-watermark');
			}
		}).each(function(){
			handleWatermark($(this));
		}).blur(function(){
			handleWatermark($(this));
		});			
	});	
})();