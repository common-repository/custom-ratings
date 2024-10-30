<?php if (!empty($root_post_id)) { ?>
	<div class="<?php echo $css_wrapper_class; ?>" >
		<div id="wpcr__ratings-vote-container-<?php echo $root_post_id ?>" class="wpcr__ratings-vote-container wpcr__ratings-object wpcr-cf"  data-root-post-id="<?php echo $root_post_id ?>" data-ajax-nonce="<?php echo wp_create_nonce("custom-rating-post-nonce-" . $root_post_id  );  ?>" >
			
				<div class="wpcr__ratings-vote-container__welcome-spinner ispinner gray animating">
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
					<div class="ispinner-blade"></div>
				</div>
				
				<?php // The following text in expressed through document.write in order to bypass it being displayed in the_excerpt, since it calls the_content. Since it is not plain text, it is stripped out.  ?>
				<p class="wpcr__ratings-vote-container__welcome-text" ><script>document.write("<?php _e('Loading Custom Ratings...', 'custom-ratings'); ?>");</script></p> 
		</div>
	</div>
<?php } ?>
