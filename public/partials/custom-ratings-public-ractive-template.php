	

	<script id='wpcr__ratings-display-template' type='text/ractive'>
		{{#if wpcr_data.show_tally_display}}
		<section >
			<span class="wpcr__star-rating-display <?php echo $star_type; ?> {{wpcr_data.rating_avg_class[0]}}" >
				<div class="wpcr__star-rating-display__foreground-container">
					<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 1 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="foreground" />
				</div>
				<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 1 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="background" />
			</span>
			<span class="wpcr__star-rating-display <?php echo $star_type; ?> {{wpcr_data.rating_avg_class[1]}}" >
				<div class="wpcr__star-rating-display__foreground-container">
					<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 2 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="foreground" />
				</div>
				<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 2 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="background" />
			</span>
			<span class="wpcr__star-rating-display <?php echo $star_type; ?> {{wpcr_data.rating_avg_class[2]}}" >
				<div class="wpcr__star-rating-display__foreground-container">
					<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 3 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="foreground" />
				</div>
				<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 3 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="background" />
			</span>
			<span class="wpcr__star-rating-display <?php echo $star_type; ?> {{wpcr_data.rating_avg_class[3]}}" >
				<div class="wpcr__star-rating-display__foreground-container">
					<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 4 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="foreground" />
				</div>
				<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 4 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="background" />
			</span>
			<span class="wpcr__star-rating-display <?php echo $star_type; ?> {{wpcr_data.rating_avg_class[4]}}" >
				<div class="wpcr__star-rating-display__foreground-container">
					<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 5 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="foreground" />
				</div>
				<img src="<?php echo $star_img_url ?>" alt="<?php _e('rating display: 5 star', 'custom-ratings'); ?>" style="width:<?php echo $tally_image_width; ?>px" class="background" />
			</span>
			{{#if wpcr_data.show_vote_count}}
			<span class="wpcr__star-rating-display-count" >
				({{wpcr_data.rating_total}})
			</span>
			{{/if}}
		</section>
		<!-- This is used to add consistent spacing at the top and bottom of the custom ratings component -->
		<p aria-hidden="true">&nbsp;</p>
		{{/if}}
	</script> 

	<script id='wpcr__ratings-vote-template' type='text/ractive'>
		<section>
			<!-- This is used to add consistent spacing at the top and bottom of the custom ratings component -->
			<p aria-hidden="true" style="<?php echo $top_border_style_string; ?>" >&nbsp;</p>
			<h3><?php _e($intro_text, 'custom-rating' ); ?></h3>
			
			<div class="wpcr__star-rating-section" >
				{{#if wpcr_data.status == 'voted' }}
					<p>
						<a class="wpcr__star-rating disabled <?php echo $star_type; ?> {{wpcr_data.my_vote_class[0]}}" disabled href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >1</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('voted 1 star', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
						<a class="wpcr__star-rating disabled <?php echo $star_type; ?> {{wpcr_data.my_vote_class[1]}}" disabled href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >2</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('voted 2 stars', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
						<a class="wpcr__star-rating disabled <?php echo $star_type; ?> {{wpcr_data.my_vote_class[2]}}" disabled href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >3</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('voted 3 stars', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
						<a class="wpcr__star-rating disabled <?php echo $star_type; ?> {{wpcr_data.my_vote_class[3]}}" disabled href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >4</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('voted 4 stars', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
						<a class="wpcr__star-rating disabled <?php echo $star_type; ?> {{wpcr_data.my_vote_class[4]}}" disabled href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >5</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('voted 5 stars', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
					</p>
				{{else}}
					<p>
						<a data-value="1" class="wpcr__star-rating <?php echo $star_type; ?> " href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >1</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('vote 1 star', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
						<a data-value="2" class="wpcr__star-rating <?php echo $star_type; ?> " href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >2</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('vote 2 stars', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
						<a data-value="3" class="wpcr__star-rating <?php echo $star_type; ?> " href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >3</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('vote 3 stars', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
						<a data-value="4" class="wpcr__star-rating <?php echo $star_type; ?> " href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >4</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('vote 4 stars', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
						<a data-value="5" class="wpcr__star-rating <?php echo $star_type; ?> " href="#" >
							<span class="wpcr__star-rating__mobile-label" style="<?php echo $circle_background_inline_css; ?>" >5</span>
							<img src="<?php echo $star_img_url ?>" alt="<?php _e('vote 5 stars', 'custom-ratings'); ?>" style="max-width:<?php echo $max_image_width; ?>px"  />
						</a>
					</p>
				{{/if}}
			</div>
	 
			<div class="wpcr__report-section" >

				{{#if wpcr_data.status == 'processing' }}
					<p class="wpcr__report-section__status wpcr-cf" >
						<span class="wpcr__report-section__icon--loading" aria-hidden="true" >
							<div class="ispinner gray animating">
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
								<div class="ispinner-blade"></div>
							</div>
						</span> 
						<span class="wpcr__report-section__loading-text">
							<?php  _e($adding_vote_text, 'custom-rating'); ?>
						</span>
					</p>
				{{elseif wpcr_data.status == 'error' }}
					<p class="wpcr__report-section__status" > <span class="fa fa-exclamation-triangle wpcr__report-section__icon--error" aria-hidden="true" ></span> <?php _e($error_text, 'custom-rating'); ?></p>
				{{elseif wpcr_data.status == 'voted' }}
					<p class="wpcr__report-section__status" > <span class="fa fa-check wpcr__report-section__icon--voted" aria-hidden="true" ></span>  <?php _e($thank_you_text, 'custom-rating'); ?></p>
				{{else}}
					<p class="wpcr__report-section__status" aria-hidden="true" >&nbsp;</p>
				{{/if}}
				<p class="wpcr__report-section__report" >
					{{#if parseInt(wpcr_data.rating_total) > 0 }}
						{{{render_report_text(wpcr_data.rating_avg, wpcr_data.rating_total)}}}
					{{else}}
						<?php _e($first_vote_text, 'custom-rating'); ?>
					{{/if}}
				</p>
				<!-- This is used to add consistent spacing at the top and bottom of the custom ratings component -->
				<p aria-hidden="true" style="<?php echo $bottom_border_style_string; ?>"  >&nbsp;</p>				
			</div>
		</section> 
	</script> 