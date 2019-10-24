var $ = jQuery.noConflict();
// Set a cookie

$(document).ready(function () {
	var instagram_update_ajax = $('#btn_instagram_update'),
		update_loader = $('#instagram_update_loader');
	
	instagram_update_ajax.click(function(e) {
		update_loader.html('<div class="app-loading"><svg class="spinner-load" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div>');
		
		$.ajax({
			url : instagram_apps_back_ajax.ajaxURL,
			type : 'POST',
			data : {
				action : 'instagram_update',
				nonce : instagram_apps_back_ajax.nonce
			},
			success : function( data ){
				console.log(data);
				update_loader.hide().html('<h3 class="msg-success">Success!</h3>' ).fadeIn();
			},
			error : function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR);
				console.log(textStatus);
				console.log(errorThrown);
				
			}
		});
		return false;
	});
	
	
});