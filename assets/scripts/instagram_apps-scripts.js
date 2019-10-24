(function($) {
	
	
	$(document).ready(function () {
		
		$("#instagram-container #loadMoreShortcode").click(function (e) {
			e.preventDefault();
			const _this = $(this); //this is the post id
			const insta_container = _this.parents('#instagram-container').find('.instagram-wrapper');
			var tags = insta_container.attr('data-hashtags');
			var count_post = insta_container.find('.grid-item--insta').length;
			$.ajax({
				url: instagram_apps_ajax.ajaxURL_front,
				type : 'POST',
				data: {
					'action' : 'knm_instagram_load_more',
					count_post : count_post,
					tags : tags
				},
				success: function(data) {
					data != '' ? insta_container.append( data ) : _this.addClass('not-active');
				},
				error : function(jqXHR, textStatus, errorThrown) {
					console.log(errorThrown);
					console.log(textStatus);
				}
				
			});
			return false;
		});
		
		
	});
	
	
}) (jQuery);