
jQuery(document).ready(function($){
	/**
	 *
	 * Choose a custom star rating image from the media library
	 *
	 */
	var mediaUploader;

	$('#upload-button').click(function(e) {
		e.preventDefault();
		// If the uploader object has already been created, reopen the dialog
			if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
			text: 'Choose Image'
		}, multiple: false });

		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on('select', function() {
			attachment = mediaUploader.state().get('selection').first().toJSON();

			// Add image attachment data to the appropriate fields 
			$('#wpcr-image-upload-url').val(attachment.url);
			$('#wpcr-image-upload-id').val(attachment.id);
			$('#wpcr-image-upload-img-preview').attr('src', attachment.url);
			$('#wpcr-image-upload-img-preview').attr('alt', attachment.alt);
		});

		// Open the uploader dialog
		mediaUploader.open();
	});


	/**
	 *
	 * Show/Hide the custom star rating image controls
	 *
	 */
	$('input[name=wpcr_star_type]').on('change', function(e){
		//console.log('val: ' + this.value);
		if (this.value == 'custom') {
			$('#wpcr__image-upload-container').removeClass('wpcr--hidden');
		} else {
			$('#wpcr__image-upload-container').addClass('wpcr--hidden');
		}
	});

	if ($('input[name=wpcr_star_type]:checked').val() == 'custom') {
		$('#wpcr__image-upload-container').removeClass('wpcr--hidden');
	} 


	/**
	 *
	 * Tab Control
	 *
	 */
	$('ul.wpcr__tabs li').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('ul.wpcr__tabs li').removeClass('wpcr--current');
		$('.wpcr__tab-content').removeClass('wpcr--current');

		$(this).addClass('wpcr--current');
		$("#"+tab_id).addClass('wpcr--current');
	})


	/**
	 *
	 * Spectrum Color Picker
	 *
	 */
	$("#wpcr_spectrum_color").spectrum({
		showAlpha: true,
		showInput: true,
		allowEmpty:true,
		chooseText: custom_ratings.color_picker.select,
		cancelText: custom_ratings.color_picker.cancel,
		change: function(color) {
			var rgba_color = color.toRgbString();
			$('input#wpcr_color').val(rgba_color);
		}
	});


	/**
	 *
	 * Alert user if navigating away from page with unsaved changes
	 *
	 */
	$('form').areYouSure();

});