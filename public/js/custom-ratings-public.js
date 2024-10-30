
(function( $ ) {
	
	var helpers = Ractive.defaults.data;
	
	helpers.render_report_text = function(rating_avg, rating_total){
		var report_string = wpcr_localized.report_string;
		var find 					= "%AVG%";
		var regex 				= new RegExp(find, "g");
		
		if (wpcr_localized.decimal_sep == ',') {
			rating_avg = rating_avg.toString().replace('.',',');
		}
		
		report_string 		= report_string.replace(regex, '<strong>' + rating_avg + '</strong>');
		find 							= '%TOTAL_VOTES%';
		regex 						= new RegExp(find, "g");
		report_string 		= report_string.replace(regex, '<strong>' + rating_total + '</strong>');

		return report_string;
	}


	var wpcr = (function() {
		var my_votes                  = {};
		var rating_data               = {};
		var post_ids                  = [];
		var rating_display_ractive    = [];
		var rating_vote_ractive       = {};

		var get_ratings = function() {

			var ratings = localStorage.getItem('wpcr_custom_ratings');
			if (ratings == null) {
				return {};
			} else {
				return JSON.parse(ratings);
			}

		};


		var my_votes                  = get_ratings();
		var ratingInfo = function(post_id, total_votes, total_vote_value ) {

			var init_average, show_half_star, class_text, show_vote_count, star_class = [];

			init_average              = Math.round(total_vote_value / total_votes * 100) / 100;
			init_average_int_val      = Math.floor(init_average);
			init_average_decimal_val  = init_average - init_average_int_val;

			if (init_average_decimal_val >= 0.5) {
				show_half_star = 1;
			} else {
				show_half_star = 0;
			}

			for (i = 1; i <= 5; i++) { 
				if (init_average_int_val >= i ) {
					class_text = 'wpcr__star-rating-display--full';
				} else {
					if (show_half_star) {
						show_half_star = 0;
						class_text = 'wpcr__star-rating-display--half';
					} else {
						class_text = '';
					}
				}

				star_class[i - 1] = class_text;

			}

			if (!total_votes) {
				init_average = 0;
			}

			show_vote_count = 0;
			if (wpcr_localized.show_vote_count ) {
				show_vote_count = 1;

			}

			show_tally_display = 1;
			if (!parseInt(total_votes) && wpcr_localized.hide_tally_if_no_votes) {
				show_tally_display = 0;
			} 

			rating_data[post_id].rating_avg_class = star_class;
			rating_data[post_id].rating_avg       = init_average;
			rating_data[post_id].rating_total     = total_votes;
			rating_data[post_id].total_vote_value = total_vote_value;
			rating_data[post_id].show_vote_count 	= show_vote_count;
			rating_data[post_id].show_tally_display 	= show_tally_display;
		};


		var get_rating_data_ajax = function() {

			jQuery.ajax({
				type : "get",
				dataType : "json",
				url : wpcr_localized.ajaxurl,
				data : {action: "wpcr_get_vote_counts", post_ids : post_ids.join(',')},
				success: function(response) {
					if(response.type == "success") {
						var data = response.rating_data;
						for (var id in data) {
							if (data.hasOwnProperty(id)) {
								ratingInfo(id , parseInt(data[id].total_vote_count), parseInt(data[id].total_vote_value));
							}
						}
						initRactive();
					} else {

					}
				},
				error: function() {

				}
			});    
		};


		var init = function() {
			$('.wpcr__ratings-object').each(function(){
				post_ids.push($(this).data('root-post-id') );
				rating_data[$(this).data('root-post-id')] = {};
			});
			get_rating_data_ajax();
			
		};

		var adjust_rating_tally_image_height = function() {
			var image_width = wpcr_localized.rating_image_set_w;
			var image_size_ratio = wpcr_localized.rating_image_h / wpcr_localized.rating_image_w;
			var image_height = image_width * image_size_ratio;

			$('.wpcr__star-rating-display').height(image_height);
			$('.wpcr__star-rating-display img').height(image_height);
			$('.wpcr__star-rating-display__foreground-container').height(image_height);

			$('.wpcr__star-rating-display').width(image_width);
			$('.wpcr__star-rating-display img').width(image_width);
			$('.wpcr__star-rating-display__foreground-container').width(image_width);

		};

		var addVote = function(post_id) {
			var my_vote 								= parseInt(my_votes[post_id]);
			var my_vote_star_classes 		= ['','','','',''];			
			if (typeof rating_data[post_id] == 'undefined') {
				rating_data[post_id] = {};
			}			
			rating_data[post_id].status = 'voted';

			for (i = 0; i < my_vote; i++) { 
				my_vote_star_classes[i] = 'wpcr__star-rating--full';
			}
			rating_data[post_id]['my_vote_class'] = my_vote_star_classes;
		};


		var initRactive = function() {
			$('.wpcr__ratings-display-container').each(function(){
				post_id = $(this).data('root-post-id');

				rating_display_ractive[0] = new Ractive({
					el: '#wpcr__ratings-display-container-' + post_id,
					template: '#wpcr__ratings-display-template',
					data: { wpcr_data : rating_data[post_id]}
				});
			});

			$('.wpcr__ratings-vote-container').each(function(){
				$this = $(this);
				post_id = $this.data('root-post-id');

				if (my_votes[post_id] != null) {
					// if a vote value exists for the post in local storage, then we know a vote has been made for this post and update the voting data accordingly
					addVote(post_id);
				}
				
				rating_vote_ractive[post_id] = new Ractive({
					el: '#wpcr__ratings-vote-container-' + post_id,
					template: '#wpcr__ratings-vote-template',
					data: { wpcr_data : rating_data[post_id] }
				});
				
			});

			adjust_rating_tally_image_height();
			registerClickEventHandler();
			registerHoverEventHandlers();
		};


		var registerClickEventHandler = function(){
			jQuery(".wpcr__star-rating" ).on('click',function(e) {
				e.preventDefault();
				if (! $(this).hasClass('disabled') ) { 
					var $parent_container   = $(this).closest('.wpcr__ratings-vote-container');
					var post_id             = $parent_container.data('root-post-id'); 
					var nonce               = $parent_container.data('ajax-nonce'); 
					var val                 = parseInt($(this).data('value')); 
					if (typeof rating_data[post_id] == 'undefined') {
						rating_data[post_id] = {};
					}
					rating_data[post_id].status = 'processing';
					rating_vote_ractive[post_id].set( 'wpcr_data', rating_data[post_id] );
					
					jQuery.ajax({
						type : "post",
						dataType : "json",
						url : wpcr_localized.ajaxurl,
						data : {action: "wpcr_add_vote", post_id : post_id, nonce: nonce, val : val},
						success: function(response) {
							if(response.type == "success") {
									
								my_votes[post_id] = val;
								addVote(post_id);
								localStorage.setItem('wpcr_custom_ratings', JSON.stringify(my_votes));
								ratingInfo(post_id, parseInt(rating_data[post_id].rating_total) + 1, parseInt(rating_data[post_id].total_vote_value) + val );
								rating_vote_ractive[post_id].set( 'wpcr_data', rating_data[post_id] );
								
							} else {
								rating_data[post_id].status = 'error';
								rating_vote_ractive[post_id].set( 'wpcr_data', rating_data[post_id] );
							}
						},
						error: function() {
							rating_data[post_id].status = 'error';
							rating_vote_ractive[post_id].set( 'wpcr_data', rating_data[post_id] );
						}
					});
				}
			});
		};


		var registerHoverEventHandlers = function() {

			$(".wpcr__star-rating").hover(
				function(e) {
					var $star_container = $(this).parent();
					var idx = $(this).index();
					if (!$(this).hasClass('disabled') ) {
						$star_container.children().each( function( index, element ){
							if (index < idx) {
								$(this).attr('class', 'wpcr__star-rating wpcr__star-rating--full');
							}
						});
						$(this).attr('class', 'wpcr__star-rating wpcr__star-rating--full');
					}

				}, function(e) {
					var $star_container = $(this).parent();
					if (!$(this).hasClass('disabled') ) {
						$star_container.children().each( function( index, element ){
							$(this).attr('class', 'wpcr__star-rating');
								
						});
					}
				}
			);

			$(".wpcr__star-rating").click( function(e) {
				e.preventDefault();
				var $star_container = $(this).parent();
				var idx = $(this).index();
				if (!$(this).hasClass('disabled') ) {
					$star_container.children().each( function( index, element ){
						if (index < idx) {
							$(this).attr('class', 'wpcr__star-rating wpcr__star-rating--full');
							$(this).data( 'star-status', 'wpcr__star-rating wpcr__star-rating--full');
						}
					});
					$(this).attr('class', 'wpcr__star-rating wpcr__star-rating--full');
				}
			});
		} 

		return {
			init : init
		};
	})();
	
	
	$( document ).ready(function() {
		wpcr.init();
	}); 


})( jQuery );

