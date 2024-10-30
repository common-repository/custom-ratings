<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/admin
 */
class Custom_Ratings_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $custom_ratings    The ID of this plugin.
	 */
	private $custom_ratings;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $custom_ratings       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $custom_ratings, $version ) {

		$this->custom_ratings = $custom_ratings;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->custom_ratings, plugin_dir_url( __FILE__ ) . 'css/custom-ratings-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'spectrum-css', plugin_dir_url( __FILE__ ) . 'css/spectrum.css', array(), $this->version, 'all' );
	}

	/**
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( isset($_GET['page']) && $_GET['page'] == 'custom-ratings-options-page' ) {
			
			wp_enqueue_script( 'spectrum-js', plugin_dir_url( __FILE__ ) . 'js/spectrum.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'are-you-sure-js', plugin_dir_url( __FILE__ ) . 'js/jquery.are-you-sure.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'are-you-sure-shim-js', plugin_dir_url( __FILE__ ) . 'js/ays-beforeunload-shim.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 
				$this->custom_ratings, plugin_dir_url( __FILE__ ) . 'js/custom-ratings-admin.js', 
				array( 'jquery', 'spectrum-js', 'are-you-sure-js', 'are-you-sure-shim-js' ), 
				filemtime(plugin_dir_path( __FILE__ ) . 'js/custom-ratings-admin.js'), 
				false 
			);

			$color_picker_strings = array(
					'select' => __('Select', 'custom-ratings'),
					'cancel' => __('Cancel', 'custom-ratings')
				); 


			$custom_ratings_data = array(
				'color_picker' => $color_picker_strings
			);
			
			wp_localize_script( $this->custom_ratings, 'custom_ratings', $custom_ratings_data);

			wp_enqueue_media();
		}
	 
	}


	/**
	 *	Add rating columns to enabled post types
	 * 
	 * @since    1.5.0
	 */
	public function add_post_listing_column( $post_types, $priority = 10 ) {
		if ( !is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}
		foreach ( $post_types as $post_type ) {
			$filter_name 	= 'manage_'.$post_type.'_posts_columns';
			$action_name 	= 'manage_'.$post_type.'_posts_custom_column';
			$sort_name 		= 'manage_edit-'.$post_type.'_sortable_columns';

			add_filter( $filter_name , function($columns) use ($priority ) {
				$star_img_url = Custom_Ratings::get_rating_object_image_src();

				return array_merge($columns,
					array('wpcr-rating' => '<span class="wpcr__ratings-column-label" ><img src="'. $star_img_url .'" alt="star" class="wpcr__admin-rating-icon"/><span class="text" >Rating</span></span>'));	
			}, $priority );

			add_action( $action_name, function( $col, $pid ) {
					switch ( $col ) {
			 
						case 'wpcr-rating' :
							require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-ratings-post-data.php';
							echo number_format(floatval($total_vote_avg), 2, $decimal_val, ' ') . ' / 5';
							break;
					};
				}, $priority, 2 );

			add_filter( $sort_name, function($columns) {
				$columns['wpcr-rating'] = 'wpcr-rating';
				return $columns;
			} );

		}
	}


	/**
	 *	Order callback for admin post listing sorting
	 * 
	 * @since    1.5.0
	 */
	public function wpcr_orderby( $query ) {
			if( ! is_admin() )
					return;
	 
			$orderby = $query->get( 'orderby');
	 
			if( 'wpcr-rating' == $orderby ) {
					$query->set('meta_key','_wpcr_rating_stars_avg');
					$query->set('orderby','meta_value_num');
			}
	}

	/**
	* Add meta boxes to 'custom ratings' enabled post types 
	*
	* @since 1.0.0 
	**/
	function admin_single_add_meta_box() {
		// add the metabox to posts and pages
		$selected_post_types = get_option('wpcr_post_types');
		$star_img_url = Custom_Ratings::get_rating_object_image_src();

		foreach ($selected_post_types as $selected_post_type ) {
			add_meta_box(
				'wpcr_custom_rating_post_meta',
				 '<img src="'. $star_img_url .'" alt=""  class="wpcr__admin-rating-icon" /> ' . __( 'Custom Ratings', 'custom-rating' ),
				array(&$this,'admin_single_metabox_callback'),
				$selected_post_type, 
				'normal',
				'high'
			);		
		}
	}

	function admin_single_metabox_callback( $post ) {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-ratings-post-data.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/custom-ratings-post-admin-display.php';

	}	



	/**
	*
	* Ajax callback to add vote
	* 
	* @since 1.0.0 
	**/
	function wpcr_add_vote() {
		$primary_post_id = $_REQUEST["post_id"];
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "custom-rating-post-nonce-" . $primary_post_id)) {
			exit("error");
		}   

		$star_rating_values = get_post_meta($primary_post_id, '_wpcr_rating_stars', true);

		if (!$star_rating_values) {
			$star_rating_values = array(0,0,0,0,0);
		}

		$star_rating_index = intval($_REQUEST["val"]) - 1;
		$star_rating_values[ $star_rating_index ] += 1; 

		$update_star_count_status = update_post_meta( $primary_post_id, '_wpcr_rating_stars', $star_rating_values );	  		
		
		$total_vote_value = 0;
		$total_vote_count = 0;
		for ($i = 0; $i < count($star_rating_values); $i++) {
			$total_vote_value += $star_rating_values[$i] * ($i + 1);
			$total_vote_count += $star_rating_values[$i];
		}

		update_post_meta( $primary_post_id, '_wpcr_rating_stars_value', $total_vote_value );		  
		update_post_meta( $primary_post_id, '_wpcr_rating_stars_count', $total_vote_count );
		update_post_meta( $primary_post_id, '_wpcr_rating_stars_avg', round($total_vote_value / $total_vote_count, 2) );

		if($update_star_count_status === false) {
			$result['type'] = "error";
		} else {
			$result['type'] = "success";
		}

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$result = json_encode($result);
			echo $result;
		}
		else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();

	}


	/**
	*
	* Ajax callback to get vote counts and ratings
	*
	* @since  1.0.0
	**/
	function wpcr_get_vote_counts() {
		global $post;
		$vote_data = array();
		$caching_time = get_option('wpcr_ajax_get_caching_time');
		if (empty($caching_time)) {
			$caching_time = 0;
		}


		$vote_query = new WP_Query( array( 
			'post__in' => explode(',',$_REQUEST['post_ids']),
			'posts_per_page'=> -1,
			'post_type' => 'any',
			'suppress_filters' => true
			) );

		if ( $vote_query->have_posts() ) {
			//echo 'has posts';
			while ( $vote_query->have_posts() ) {
				//echo 'in loop';
				$vote_query->the_post();
				$vote_counts_raw = get_post_meta(get_the_id(), '_wpcr_rating_stars', true);
				$total_vote_value = 0;
				$total_vote_count = 0;
				if (!empty($vote_counts_raw)){
					for ($i = 0; $i < 5; $i++) {
						$total_vote_count += $vote_counts_raw[$i];
						$total_vote_value += $vote_counts_raw[$i] * ($i + 1);
					}
				}
				
				$vote_data[get_the_id()]['total_vote_count'] = $total_vote_count;
				$vote_data[get_the_id()]['total_vote_value'] = $total_vote_value;

			}
		}
		
		$result = array(
			'type' => 'success',
			'rating_data' => $vote_data
		);

	
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			header_remove("Pragma");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s \G\M\T", time()));
			header("Expires: " . gmdate("D, d M Y H:i:s \G\M\T", time()+intval($caching_time)));
			header("Cache-Control: public, max-age=" . $caching_time);

			$result = json_encode($result);
			echo $result;
		} else {
			header("Location: ".$_SERVER["HTTP_REFERER"]);
		}

		die();
				
	}


	function plugin_update_check() {
		if (get_site_option( 'wpcr_plugin_version' ) != $this->version) {
			update_option( 'wpcr_plugin_version', $this->version);

		}
	}


	public static function set_initial_rating_values() {
		global $post;

		$enabled_post_types 	= get_option('wpcr_post_types');
		$update_string = array();
		if (!empty($enabled_post_types)) {
			
			$args = array(
				'post_type' => $enabled_post_types
			);                  
			
			$rating_post_type_enabled_query = new WP_Query( $args );

			if ( $rating_post_type_enabled_query->have_posts() ) : while ( $rating_post_type_enabled_query->have_posts() ) : $rating_post_type_enabled_query->the_post(); 
				
				add_post_meta( $post->ID, '_wpcr_rating_stars_avg', 0, true );
				add_post_meta( $post->ID, '_wpcr_rating_stars_value', 0, true );
				add_post_meta( $post->ID, '_wpcr_rating_stars_count', 0, true );
				$update_string[] = $post->ID . ' ' . get_post_type( get_the_ID() );
			endwhile; 
		 endif;

		 $update_string; 
		}



	}

	//do_action( "update_option_wpcr_post_types", $oldvalue, $_newvalue );

	function enabled_post_type_change($oldvalue, $_newvalue) {
		global $post;

		$new_enabled_post_types = array_diff($_newvalue, $oldvalue);
		$update_string = array();
		if (!empty($new_enabled_post_types)) {
		 
			$args = array(
				'post_type' => $new_enabled_post_types
			);                  
			
			$rating_post_type_enabled_query = new WP_Query( $args );

			if ( $rating_post_type_enabled_query->have_posts() ) : while ( $rating_post_type_enabled_query->have_posts() ) : $rating_post_type_enabled_query->the_post(); 
				
				add_post_meta( $post->ID, '_wpcr_rating_stars_avg', 0, true );
				add_post_meta( $post->ID, '_wpcr_rating_stars_value', 0, true );
				add_post_meta( $post->ID, '_wpcr_rating_stars_count', 0, true );

				$update_string[] = $post->ID . ' ' . get_post_type( get_the_ID() );

			endwhile; 
		 endif;

		 $update_string; 
		}

	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function wpcr_enabled_post_save( $post_id ) {


		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if ( false !== wp_is_post_revision( $post_id ) )
				return;

		$enabled_post_types = get_option('wpcr_post_types');
		$post_type = get_post_type( $post_id );
		if ( !in_array($post_type, $enabled_post_types) ) {
			return;

		}		

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		// add initial metadata values to ensure orderby queries include this post
		add_post_meta( $post_id, '_wpcr_rating_stars_avg', 0, true );
		add_post_meta( $post_id, '_wpcr_rating_stars_value', 0, true );
		add_post_meta( $post_id, '_wpcr_rating_stars_count', 0, true );

	}

	/**
	 *
	 * Add options page for Custom Ratings Settings
	 *
	 * @since 1.0.0 
	 */
	function options_menu() {
		add_options_page( 
			'Custom Rating Settings',
			'Custom Ratings',
			'manage_options',
			'custom-ratings-options-page',
			array(&$this,'custom_ratings_settings_page')
		);
	}


	/**
	 *
	 * Custom Ratings Settings Page Callback
	 *
	 * @since 1.0.0 
	 */
	function custom_ratings_settings_page() {
		
		?>
			<div class="wrap">
				<h1><?php _e('Custom Ratings Settings', 'custom-ratings'); ?></h1>
				
				<form method="post" action="options.php">
					<div class="container">

						<ul class="wpcr__tabs wpcr--clearfix">
							<li class="wpcr__tab-link wpcr--current" data-tab="tab-general"><?php _e('General', 'custom-ratings'); ?></li>
							<li class="wpcr__tab-link" data-tab="tab-visual-display"><?php _e('Visual Display', 'custom-ratings'); ?></li>
							<li class="wpcr__tab-link" data-tab="tab-text"><?php _e('Text', 'custom-ratings'); ?></li>
							<li class="wpcr__tab-link wpcr--settings-save-tab " ><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'custom-ratings'); ?>"></li>
						</ul>

						<div id="tab-general" class="wpcr__tab-content wpcr--current">
							<?php do_settings_sections("custom-ratings-options-page-general");  ?>
						</div>
						<div id="tab-visual-display" class="wpcr__tab-content">
							 <ul class="wpcr__star-type-list">
								<?php do_settings_sections("custom-ratings-options-star-type");  ?>
							 </ul>
							 <?php do_settings_sections("custom-ratings-options-page-visual-display");  ?>
							 
						</div>
						<div id="tab-text" class="wpcr__tab-content">
							<?php do_settings_sections("custom-ratings-options-page-text");  ?>
						</div>
						
					</div><!-- container -->

					<?php
						
						settings_fields("custom-ratings-settings");
								 
						//submit_button(); 
					?>          
				</form>
			</div>
		<?php
	}


	/**
	 *
	 * Option field for built in star types and activation of a custom star type
	 *
	 * @since 1.0.0 
	 */
	function display_star_type_field_element() {
		?>
			
				<li>
					<input type="radio" id="wpcr__star-label-cupcake" name="wpcr_star_type" value="cupcake" <?php checked('cupcake', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-cupcake"  >
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/cupcake.gif' ?>" alt="<?php _e('Cupcake', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-cupcake" class="wpcr__star-type-label" >
						<?php _e('Cupcake', 'custom-ratings'); ?>
					</label>
				</li>
				<li>
					<input type="radio" id="wpcr__star-label-money" name="wpcr_star_type" value="money" <?php checked('money', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-money" >
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/cash.png' ?>" alt="<?php _e('Money', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-money" class="wpcr__star-type-label" >
						<?php _e('Money', 'custom-ratings'); ?>
					</label>
				</li>
				<li>
					<input type="radio" id="wpcr__star-label-cat" name="wpcr_star_type" value="cat" <?php checked('cat', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-cat" >
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/cat.gif' ?>" alt="<?php _e('Cat', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-cat" class="wpcr__star-type-label" >
						<?php _e('Cat', 'custom-ratings'); ?>
					</label>
				</li>
				<li>
					<input type="radio" id="wpcr__star-label-carrot" name="wpcr_star_type" value="carrot" <?php checked('carrot', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-carrot" >
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/carrot.png' ?>" alt="<?php _e('Carrot', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-carrot" class="wpcr__star-type-label" >
						<?php _e('Carrot', 'custom-ratings'); ?>
					</label>
				</li>
				<li>
					<input type="radio" id="wpcr__star-label-frog-weightlift" name="wpcr_star_type" value="frog-weightlift" <?php checked('frog-weightlift', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-frog-weightlift" >
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/frog-weightlift.gif' ?>" alt="<?php _e('Weightlifting Frog', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-frog-weightlift" class="wpcr__star-type-label" >
						<?php _e('Weightlifting Frog', 'custom-ratings'); ?>
					</label>
				</li>
				<li>
					<input type="radio" id="wpcr__star-label-ron-burgundy" name="wpcr_star_type" value="ron-burgundy" <?php checked('ron-burgundy', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-ron-burgundy" >
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/ron-burgundy.jpg' ?>" alt="<?php _e('Ron Burgundy', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-ron-burgundy" class="wpcr__star-type-label" >
						<?php _e('Ron Burgundy', 'custom-ratings'); ?>
					</label>
				</li>
				<li>
					<input type="radio" id="wpcr__star-label-star" name="wpcr_star_type" value="star" <?php checked('star', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-star">
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/star.png' ?>" alt="<?php _e('Star', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-star" class="wpcr__star-type-label" >
						<?php _e('Star', 'custom-ratings'); ?>
					</label>
				</li>
				<li>
					<input type="radio" id="wpcr__star-label-wine-glass" name="wpcr_star_type" value="wine-glass" <?php checked('wine-glass', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-wine-glass" >
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/wine-glass.png' ?>" alt="<?php _e('Wine Glass', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-wine-glass" class="wpcr__star-type-label" >
						<?php _e('Wine Glass', 'custom-ratings'); ?>
					</label>
				</li>
				<li>
					<input type="radio" id="wpcr__star-label-heart" name="wpcr_star_type" value="heart" <?php checked('heart', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-heart" >
						<img class="wpcr__star-type-list__star-img" src="<?php echo  plugin_dir_url(dirname( __FILE__ )) . 'public/images/heart.png' ?>" alt="<?php _e('Heart', 'custom-ratings'); ?>" />	
					</label>
					<label for="wpcr__star-label-heart" class="wpcr__star-type-label" >
						<?php _e('Heart', 'custom-ratings'); ?>
					</label>
				</li>				
				<li>
					<input type="radio" id="wpcr__star-label-custom" name="wpcr_star_type" value="custom" <?php checked('custom', get_option('wpcr_star_type') ); ?> />
					<label for="wpcr__star-label-custom" class="wpcr__star-type-label" >
						<?php _e('Custom (Upload your own image. Images of at least 100px X 100px work best.)', 'custom-ratings'); ?>	
					</label>
				</li>
				
		
		<?php
	}


	/**
	 *
	 * Option field for custom star type image upload to media gallery
	 *
	 * @since 1.0.0 
	 */
	function display_image_upload_field_element() {
		$wpcr_image_upload_url = '';
		$wpcr_image_upload_alt = '';

		$img_obj = wp_get_attachment_image_src(get_option('wpcr_image_upload_id'), 'thumbnail' );
		if (!empty($img_obj)) {
			$wpcr_image_upload_url = $img_obj[0];
			$wpcr_image_upload_alt = get_post_meta(get_option('wpcr_image_upload_id'), '_wp_attachment_image_alt', true);    	
		}

		?>
		<div id="wpcr__image-upload-container" class="wpcr--hidden" >
			<div class="wpcr__image_upload_row">
				<input id="upload-button" type="button" class="button" value="<?php _e('Choose Image','custom-ratings') ?>" />
				<input id="wpcr-image-upload-url" type="text" value="<?php echo $wpcr_image_upload_url; ?>" disabled />
			</div>

			<input id="wpcr-image-upload-id" type="hidden" name="wpcr_image_upload_id" value="<?php echo get_option('wpcr_image_upload_id'); ?>" />
			<img  id="wpcr-image-upload-img-preview"  src="<?php echo $wpcr_image_upload_url ?>" alt="<?php echo $wpcr_image_upload_alt ?>" />
		</div>
		
		 <?php
	}


	/**
	 *
	 * Option field for introduction text used in front end display of custom rating vote component
	 *
	 * @since 1.0.0
	 */
	function display_intro_text_field_element() {
		?>
				<input type="text" name="wpcr_intro_text" class="wpcr__text" id="wpcr_intro_text" value="<?php echo get_option('wpcr_intro_text'); ?>" placeholder="<?php _e('Rate This!', 'custom-ratings') ?>" />
			<?php
	}


	/**
	 *
	 * Option field for loading text (before ajax call to get vote data returns) used in front end display of custom rating vote component 
	 *
	 * @since 1.0.0 
	 */
	function display_loading_text_field_element() {
		?>
				<input type="text" name="wpcr_loading_text" class="wpcr__text" id="wpcr_loading_text" value="<?php echo get_option('wpcr_loading_text'); ?>" placeholder="<?php _e('Loading...', 'custom-ratings') ?>" />
			<?php
	}


	/**
	 *
	 * Option field for 'adding vote' text (appears during ajax call to add the user's vote) used in front end display of custom ratings vote component
	 *
	 * @since 1.0.0
	 */
	function display_adding_vote_text_field_element() {
		?>
			<input type="text" name="wpcr_adding_vote_text" class="wpcr__text" id="wpcr_adding_vote_text" value="<?php echo get_option('wpcr_adding_vote_text'); ?>" placeholder="<?php _e('Adding your vote...', 'custom-ratings') ?>" />
		<?php
	}


	/**
	 *
	 * Option field for 'adding vote' error text in response to ajax call to add vote
	 *
	 * @since 1.0.0
	 */
	function display_error_text_field_element() {
		?>
			<input type="text" name="wpcr_error_text" id="wpcr_error_text" class="wpcr__text" value="<?php echo get_option('wpcr_error_text'); ?>" placeholder="<?php _e('An error occured while trying to add your vote. Try again later.', 'custom-ratings') ?>" />
		<?php
	}


	/**
	 *
	 * Option field for 'first vote' text 
	 *
	 * @since 1.0.0
	 */
	function display_first_vote_text_field_element() {
		?>
			<input type="text" name="wpcr_first_vote_text" id="wpcr_first_vote_text" class="wpcr__text" value="<?php echo get_option('wpcr_first_vote_text'); ?>" placeholder="<?php _e('Be the first to vote!', 'custom-ratings') ?>" />
		<?php
	}


	/**
	 *
	 * Option field for 'thank you for voting' text after a user has voted on the front end display of the custom ratings component
	 *
	 * @since 1.0.0
	 */
	function display_thank_you_text_field_element() {
		?>
			<input type="text" name="wpcr_thank_you_text" id="wpcr_thank_you_text" class="wpcr__text" value="<?php echo get_option('wpcr_thank_you_text'); ?>" placeholder="<?php _e('Thank you for voting!', 'custom-ratings') ?>" />
		<?php
	}


	/**
	 *
	 * Option field for 'report text'. This contains placeholder values for the number of votes and rating average.
	 *
	 * @since 1.0.0 
	 */
	function display_report_text_field_element() {
		?>
			
			<input type="text" name="wpcr_report_text" id="wpcr_report_text" class="wpcr__text" value="<?php echo get_option('wpcr_report_text'); ?>" placeholder="<?php _e('%TOTAL_VOTES% votes with an average of %AVG%.', 'custom-ratings') ?>" />
			<p><small><?php _e('Use the placeholders of %AVG% and %TOTAL_VOTES% in your text.', 'custom-ratings') ?></small></p>
		<?php
	}


	/**
	 *
	 * Option field to choose which post types 'custom ratings' are applied to.
	 *
	 * @since 1.0.0
	 */
	function display_post_type_choice_element() {
		
		$args = array(
			'public'   => true
		);

		$post_types 					= get_post_types( $args, 'objects' ); 
		$selected_post_types 	= get_option('wpcr_post_types');
		
		$is_selected_post_type = function($post_type_name) use ($selected_post_types) {
			if (!empty($selected_post_types) && in_array($post_type_name, $selected_post_types) ) {
				return ' selected ';
			}
		};
		print '<p><small>' . __('To select multiple post types hold down CTRL (windows) or CMD (mac) while clicking.', 'custom-ratings') . '</small></p>';
		print '<select id="wpcr_post_types" name="wpcr_post_types[]" multiple >';
		foreach ( $post_types as $post_type ) {
			echo '<option value="'. $post_type->name .'" '. $is_selected_post_type($post_type->name) .' >' . $post_type->labels->name . '</option>';
		}
		print '</select>';

	}


	/**
	 *
	 * Option field to select how the the ratings display is positioned in relation to the excerpt
	 *
	 * @since 1.0.0
	 */
	function display_excerpt_output_field_element() {
		?>
			<ul>
				<li>
					<input type="radio" id="wpcr__excerpt-output-above" name="wpcr_excerpt_output_type" value="above" <?php checked('above', get_option('wpcr_excerpt_output_type') ); ?> />
					<label for="wpcr__excerpt-output-above" ><?php _e('Above Excerpt', 'custom-ratings') ?></label>
				</li>
				<li>
					<input type="radio" id="wpcr__excerpt-output-below" name="wpcr_excerpt_output_type" value="below" <?php checked('below', get_option('wpcr_excerpt_output_type') ); ?> />
					<label for="wpcr__excerpt-output-below" ><?php _e('Below Excerpt', 'custom-ratings') ?></label>
				</li>
				<li>
					<input type="radio" id="wpcr__excerpt-output-none" name="wpcr_excerpt_output_type" value="none" <?php checked('none', get_option('wpcr_excerpt_output_type') ); ?> />
					<label for="wpcr__excerpt-output-none" ><?php _e('None (you can still manually modify the theme files to display custom ratings components)', 'custom-ratings') ?></label>
					<p><small><?php printf( __('If are manually adding the Custom Ratings component, it requires the appropriate placement of %1$s in the theme template files, within <a href="https://codex.wordpress.org/The_Loop">the loop</a>.', 'custom-ratings'), '<strong>echo Custom_Ratings_Public::display();</strong>'); ?></small></p>
				</li>
			</ul>	  
		<?php
	}


	/**
	 *
	 * Option field to select how the vote component is positioned in relation to the content 
	 *
	 * @since 1.0.0
	 */
	function display_content_output_field_element() {
		?>
			<ul>
				<li>
					<input type="radio" id="wpcr__content-output-above" name="wpcr_content_output_type" value="above" <?php checked('above', get_option('wpcr_content_output_type') ); ?> />
					<label for="wpcr__content-output-above" ><?php _e('Above Content', 'custom-ratings') ?></label>
				</li>
				<li>
					<input type="radio" id="wpcr__content-output-below" name="wpcr_content_output_type" value="below" <?php checked('below', get_option('wpcr_content_output_type') ); ?> />
					<label for="wpcr__content-output-below" ><?php _e('Below Content', 'custom-ratings') ?></label>
				</li>
				<li>
					<input type="radio" id="wpcr__content-output-none" name="wpcr_content_output_type" value="none" <?php checked('none', get_option('wpcr_content_output_type') ); ?> />
					<label for="wpcr__content-output-none" ><?php _e('None (you can still manually modify the theme files to display custom ratings components)', 'custom-ratings') ?></label>
					<p><small><?php printf( __('If are manually adding the Custom Ratings component, it requires the appropriate placement of %1$s in the theme template files, within <a href="https://codex.wordpress.org/The_Loop">the loop</a>.', 'custom-ratings'), '<strong>echo Custom_Ratings_Public::vote();</strong>'); ?></small></p>
				</li>
			</ul>	  
		<?php
	}


	/**
	 *
	 * Option field to select background colour of front end components
	 *
	 * @since 1.0.0
	 */
	function display_color_selector_field_element() {
		?>

		<input type='text' name='' id="wpcr_spectrum_color" value='<?php echo get_option('wpcr_color'); ?>' />
		<input type='hidden' name="wpcr_color" id="wpcr_color" value="<?php echo get_option('wpcr_color'); ?>" />
		<p><small><?php _e('This color will be used on borders, and number backgrounds on mobile views.', 'custom-ratings'); ?></small></p>
		<?php
	}


	/**
	 *
	 * Option field to toggle top border
	 *
	 * @since 1.0.0
	 */
	function display_top_border_field_element() {
		?>

		<input type='checkbox' name='wpcr_top_border' id="wpcr_top_border" <?php checked(get_option('wpcr_top_border'), '1'); ?> value='1' />
		<label for="wpcr_top_border" ><?php _e('Display Top Border', 'custom-ratings'); ?></label>
		<?php
	}

	/**
	 *
	 * Option field to toggle bottom border
	 *
	 * @since 1.0.0
	 */
	function display_bottom_border_field_element() {
		?>

		<input type='checkbox' name='wpcr_bottom_border' id="wpcr_bottom_border" <?php checked(get_option('wpcr_bottom_border'), '1'); ?> value='1' />
		<label for="wpcr_bottom_border" ><?php _e('Display Bottom Border', 'custom-ratings'); ?></label>
		<?php
	}

	/**
	 *
	 * Option field to toggle display of custom ratings components on main query, or everywhere
	 *
	 * @since 1.0.0
	 */
	function display_main_query_field_element() {
		?>

		<input type='checkbox' name='wpcr_only_on_main_query' id="wpcr_only_on_main_query" <?php checked(get_option('wpcr_only_on_main_query'), '1'); ?> value='1' />
		<label for="wpcr_only_on_main_query" >
			<?php _e('Only apply Custom Ratings on main section of page', 'custom-ratings'); ?> <br />
			<small><?php _e('This uses the "<a href="https://codex.wordpress.org/Function_Reference/is_main_query">is main query</a>" check.  In most cases it prevents Custom Ratings components from appearing on sidebar, footer, or header page sections.', 'custom-ratings'); ?></small>
		</label>
		<?php
	}


	/**
	 *
	 * Option field to toggle display of custom ratings components exclusively while in 'the loop'
	 *
	 * @since 1.5.0
	 */
	function display_in_loop_field_element() {
		?>

		<input type='checkbox' name='wpcr_only_in_loop' id="wpcr_only_in_loop" <?php checked(get_option('wpcr_only_in_loop'), '1'); ?> value='1' />
		<label for="wpcr_only_in_loop" >
			<?php _e('Only apply Custom Ratings while in "the loop"', 'custom-ratings'); ?> <br />
			<small><?php _e('This uses the "<a href="https://codex.wordpress.org/Function_Reference/in_the_loop">in the loop</a>" check.', 'custom-ratings'); ?></small>
		</label>
		<?php
	}


	/**
	 *
	 * Option field to toggle use of Custom Ratings CSS on front end
	 *
	 * @since 1.0.1
	 */
	function display_use_own_css_field_element() {
		?>

		<input type='checkbox' name='wpcr_use_own_css' id="wpcr_use_own_css" <?php checked(get_option('wpcr_use_own_css'), '1'); ?> value='1' />
		<label for="wpcr_use_own_css" >
			<?php _e('I will use my own CSS and disable default Custom Ratings CSS', 'custom-ratings'); ?> 
		</label>
		<?php
	}


	/**
	 *
	 * Option field to toggle display of Custom Ratings on home page
	 *
	 * @since 1.5.0
	 */
	function display_hide_home_page_field_element() {
		?>

		<input type='checkbox' name='wpcr_hide_on_home_page' id="wpcr_hide_on_home_page" <?php checked(get_option('wpcr_hide_on_home_page'), '1'); ?> value='1' />
		<label for="wpcr_hide_on_home_page" >
			<?php _e('Hide Custom Ratings components on the Home Page', 'custom-ratings'); ?> 
		</label>
		<?php
	}


	/**
	 *
	 * Option field to toggle display of Custom Ratings on archive pages
	 *
	 * @since 1.5.0
	 */
	function display_hide_archive_page_field_element() {
		?>

		<input type='checkbox' name='wpcr_hide_on_archive_page' id="wpcr_hide_on_archive_page" <?php checked(get_option('wpcr_hide_on_archive_page'), '1'); ?> value='1' />
		<label for="wpcr_hide_on_archive_page" >
			<?php _e('Hide Custom Ratings components on all Archive Pages', 'custom-ratings'); ?> 
		</label>
		<?php
	}


	/**
	 *
	 * Option field to toggle display of Custom Ratings on front page
	 *
	 * @since 1.5.0
	 */
	function display_hide_search_page_field_element() {
		?>

		<input type='checkbox' name='wpcr_hide_on_search_page' id="wpcr_hide_on_search_page" <?php checked(get_option('wpcr_hide_on_search_page'), '1'); ?> value='1' />
		<label for="wpcr_hide_on_search_page" >
			<?php _e('Hide Custom Ratings components on the Search Page', 'custom-ratings'); ?> 
		</label>
		<?php
	}


	/**
	 *
	 * Option field to toggle display of Custom Ratings on front page
	 *
	 * @since 1.0.1
	 */
	function display_hide_front_page_field_element() {
		?>

		<input type='checkbox' name='wpcr_hide_on_front_page' id="wpcr_hide_on_front_page" <?php checked(get_option('wpcr_hide_on_front_page'), '1'); ?> value='1' />
		<label for="wpcr_hide_on_front_page" >
			<?php _e('Hide Custom Ratings components on the Front Page', 'custom-ratings'); ?> 
		</label>
		<?php
	}


	/**
	 *
	 * Option field for max width of rating object
	 *
	 * @since 1.5.0
	 */
	function display_max_rating_object_width_field_element() {
		
		$max_image_width = get_option('wpcr_max_rating_object_width');
		if (!(get_option('wpcr_max_rating_object_width')) || !is_numeric(get_option('wpcr_max_rating_object_width')) ) {
			$max_image_width = '60';
		}

		?>
		<input type="number" name='wpcr_max_rating_object_width' min="1" step="1" id="wpcr_max_rating_object_width" value='<?php echo $max_image_width; ?>' />
		<span>px</span>
		<br />
		<label for="wpcr_max_rating_object_width" >
			<small><?php _e('Maximum width of each rating object image', 'custom-ratings'); ?></small> 
		</label>
		<?php
	}


	/**
	 *
	 * Option field for max width of rating object tally image
	 *
	 * @since 1.5.0
	 */
	function display_max_tally_image_width_field_element() {
		
		$max_image_width = get_option('wpcr_max_tally_image_width');
		if (!(get_option('wpcr_max_tally_image_width')) || !is_numeric(get_option('wpcr_max_tally_image_width')) ) {
			$max_image_width = '35';
		}

		?>
		<input type="number" name='wpcr_max_tally_image_width' min="1" step="1" id="wpcr_max_tally_image_width" value='<?php echo $max_image_width; ?>' />
		<span>px</span>
		<br />
		<label for="wpcr_max_tally_image_width" >
			<small><?php _e('Maximum width of each rating tally image', 'custom-ratings'); ?></small> 
		</label>
		<?php
	}


	/**
	 *
	 * Option field for decimal type
	 *
	 * @since 1.5.0
	 */
	function display_decimal_type_field_element() {
		?>
		<input type="radio" name='wpcr_decimal_type' id="wpcr_decimal_type_comma" <?php checked(get_option('wpcr_decimal_type'), 'comma'); ?> value='comma' />
		<label for="wpcr_decimal_type_comma" >
			<?php _e('Comma', 'custom-ratings'); ?> <span class="wpcr--muted_text" >(ex: 4,75)</span>
		</label>  	
		<br />
		<input type="radio" name='wpcr_decimal_type' id="wpcr_decimal_type_point" <?php if (checked(get_option('wpcr_decimal_type'), 'point', false) || !(get_option('wpcr_decimal_type'))) { echo ' checked '; }  ; ?> value='point' />
		<label for="wpcr_decimal_type_point" >
			<?php _e('Point', 'custom-ratings'); ?> <span class="wpcr--muted_text" > (ex: 4.75)</span> 
		</label>

		<?php
	}


	/**
	 *
	 * Option field to display vote count in rating display
	 *
	 * @since 1.5.0
	 */
	function display_show_vote_count_field_element() {
		?>

		<input type='checkbox' name='wpcr_show_vote_count' id="wpcr_show_vote_count" <?php checked(get_option('wpcr_show_vote_count'), '1'); ?> value='1' />
		<label for="wpcr_show_vote_count" >
			<?php _e('Show vote count for rating tally', 'custom-ratings'); ?> <br />
			
		</label>
		<?php
	}


	/**
	 *
	 * Option field to display vote count in rating display
	 *
	 * @since 1.5.0
	 */
	function display_hide_tally_if_no_votes_field_element() {
		?>

		<input type='checkbox' name='wpcr_hide_tally_if_no_votes' id="wpcr_hide_tally_if_no_votes" <?php checked(get_option('wpcr_hide_tally_if_no_votes'), '1'); ?> value='1' />
		<label for="wpcr_hide_tally_if_no_votes" >
			<?php _e('Hide rating tally if there are zero votes', 'custom-ratings'); ?> <br />
			
		</label>
		<?php
	}

	/**
	 *
	 * Option field to specify caching time for AJAX request, to get rating information
	 *
	 * @since 1.0.1
	 */
	function display_ajax_get_caching_field_element() {
		?>
		<label for='wpcr_ajax_get_caching_time' >
			<?php _e('Caching time for AJAX get rating data requests. Enter value in seconds.', 'custom-ratings'); ?> 
		</label>
		<br />
		<input type='number' name='wpcr_ajax_get_caching_time' id="wpcr_ajax_get_caching_time" value="<?php echo get_option('wpcr_ajax_get_caching_time'); ?>" min="0" placeholder="0" />
		
		<?php
	}

	function display_theme_panel_fields() {
		
		// Set up subsections for settings page
		add_settings_section("general-section", 	__("General Settings", 'custom-ratings'), 	null, "custom-ratings-options-page-general");
		add_settings_section("general-template-section", 	__("Templates", 'custom-ratings'), 	null, "custom-ratings-options-page-general");
		add_settings_section("visual-display", 		__("Visual Display", 'custom-ratings'), 		null, "custom-ratings-options-page-visual-display");
		add_settings_section("star-type", 				__("Star Type", 'custom-ratings'), 					null, "custom-ratings-options-star-type");
		add_settings_section("text-section", 			__("Text Settings", 'custom-ratings'), 			null, "custom-ratings-options-page-text");

		// General settings fields
		add_settings_field("wpcr_post_types", 							__('What post types should custom ratings be applied to?', 'custom-rating'), 				array(&$this,"display_post_type_choice_element"), 				"custom-ratings-options-page-general", "general-section");
		add_settings_field("wpcr_excerpt_output_type", 			__('Rating Tally Display Position', 'custom-rating'), 															array(&$this,"display_excerpt_output_field_element"), 		"custom-ratings-options-page-general", "general-section");
		add_settings_field("wpcr_content_output_type", 			__('Vote Display Position', 'custom-rating'), 																			array(&$this,"display_content_output_field_element"), 		"custom-ratings-options-page-general", "general-section");
		add_settings_field("wpcr_only_on_main_query", 			__('Main Query', 'custom-rating'), 																			array(&$this,"display_main_query_field_element"), 			"custom-ratings-options-page-general", "general-section");
		add_settings_field("wpcr_only_in_loop", 			__('In Loop', 'custom-rating'), 																			array(&$this,"display_in_loop_field_element"), 			"custom-ratings-options-page-general", "general-section");

		add_settings_field("wpcr_ajax_get_caching_time", 		__('Caching', 'custom-rating'), 																										array(&$this,"display_ajax_get_caching_field_element"), 			"custom-ratings-options-page-general", "general-section");		
		

		add_settings_field("wpcr_hide_on_front_page", 			__('Front Page', 'custom-rating'), 																									array(&$this,"display_hide_front_page_field_element"), 		"custom-ratings-options-page-general", "general-template-section");
		
		add_settings_field("wpcr_hide_on_home_page", 			__('Home Page', 'custom-rating'), 																									array(&$this,"display_hide_home_page_field_element"), 		"custom-ratings-options-page-general", "general-template-section");
		add_settings_field("wpcr_hide_on_search_page", 			__('Search Page', 'custom-rating'), 																									array(&$this,"display_hide_search_page_field_element"), 		"custom-ratings-options-page-general", "general-template-section");
		add_settings_field("wpcr_hide_on_archive_page", 			__('Archive Page', 'custom-rating'), 																									array(&$this,"display_hide_archive_page_field_element"), 		"custom-ratings-options-page-general", "general-template-section");



		// Star Type fields
		add_settings_field("wpcr_star_type", 								__('Choose Your Star Type', 'custom-rating'), 																			array(&$this,"display_star_type_field_element"), 					"custom-ratings-options-star-type", "star-type");	
		add_settings_field("wpcr_image_upload_id", 					__('Upload an image (select "custom" above)', 'custom-rating'), 										array(&$this,"display_image_upload_field_element"), 			"custom-ratings-options-star-type", "star-type");
		

		// Visual Display settings fields
		add_settings_field("wpcr_spectrum_color", 					__('Select Color', 'custom-rating'), 																								array(&$this,"display_color_selector_field_element"), 		"custom-ratings-options-page-visual-display", "visual-display");
		add_settings_field("wpcr_show_top_border", 					__('Display Top Border', 'custom-rating'), 																					array(&$this,"display_top_border_field_element"), 				"custom-ratings-options-page-visual-display", "visual-display");
		
		add_settings_field("wpcr_show_bottom_border", 			__('Display Bottom Border', 'custom-rating'), 																			array(&$this,"display_bottom_border_field_element"), 			"custom-ratings-options-page-visual-display", "visual-display");
		add_settings_field("wpcr_use_own_css", 							__('CSS', 'custom-rating'), 																												array(&$this,"display_use_own_css_field_element"), 				"custom-ratings-options-page-visual-display", "visual-display");
		add_settings_field("wpcr_max_rating_object_width", 							__('Rating Vote Image Width', 'custom-rating'), 																												array(&$this,"display_max_rating_object_width_field_element"), 				"custom-ratings-options-page-visual-display", "visual-display");
		add_settings_field("wpcr_max_tally_image_width", 							__('Rating Tally Image Width', 'custom-rating'), 																												array(&$this,"display_max_tally_image_width_field_element"), 				"custom-ratings-options-page-visual-display", "visual-display");		


		add_settings_field("wpcr_decimal_type", 							__('Decimal Type', 'custom-rating'), 																												array(&$this,"display_decimal_type_field_element"), 				"custom-ratings-options-page-visual-display", "visual-display");
		add_settings_field("wpcr_show_vote_count", 							__('Show Vote Count', 'custom-rating'), 																												array(&$this,"display_show_vote_count_field_element"), 				"custom-ratings-options-page-visual-display", "visual-display");
		add_settings_field("wpcr_hide_tally_if_no_votes", 							__('Hide Tally if Zero Count', 'custom-rating'), 																												array(&$this,"display_hide_tally_if_no_votes_field_element"), 				"custom-ratings-options-page-visual-display", "visual-display");
		// Text settings fields

		
		add_settings_field("wpcr_intro_text", 							__('Intro Text', 'custom-rating'), 																									array(&$this,"display_intro_text_field_element"), 				"custom-ratings-options-page-text", "text-section");
		
		add_settings_field("wpcr_loading_text", 						__('Loading Text', 'custom-rating'), 																								array(&$this,"display_loading_text_field_element"), 			"custom-ratings-options-page-text", "text-section");
		
		add_settings_field("wpcr_error_text", 							__('Add Vote Error Text', 'custom-rating'), 																				array(&$this,"display_error_text_field_element"), 				"custom-ratings-options-page-text", "text-section");
		
		add_settings_field("wpcr_first_vote_text", 					__('First Vote Text', 'custom-rating'), 																						array(&$this,"display_first_vote_text_field_element"),		"custom-ratings-options-page-text", "text-section");
		
		add_settings_field("wpcr_adding_vote_text", 				__('Adding Vote Text', 'custom-rating'), 																						array(&$this,"display_adding_vote_text_field_element"), 	"custom-ratings-options-page-text", "text-section");
		
		add_settings_field("wpcr_thank_you_text", 					__('Thank You Text', 'custom-rating'), 																							array(&$this,"display_thank_you_text_field_element"), 		"custom-ratings-options-page-text", "text-section");
		
		add_settings_field("wpcr_report_text", 							__('Rating Report Text', 'custom-rating'), 																					array(&$this,"display_report_text_field_element"), 				"custom-ratings-options-page-text", "text-section");
		
		// Register general settings
		register_setting("custom-ratings-settings", "wpcr_star_type");
		register_setting("custom-ratings-settings", "wpcr_post_types");
		register_setting("custom-ratings-settings", "wpcr_excerpt_output_type");
		register_setting("custom-ratings-settings", "wpcr_content_output_type");
		register_setting("custom-ratings-settings", "wpcr_image_upload_id");
		register_setting("custom-ratings-settings", "wpcr_color");
		register_setting("custom-ratings-settings", "wpcr_top_border");
		register_setting("custom-ratings-settings", "wpcr_bottom_border");
		register_setting("custom-ratings-settings", "wpcr_only_on_main_query");
		register_setting("custom-ratings-settings", "wpcr_only_in_loop");
		register_setting("custom-ratings-settings", "wpcr_ajax_get_caching_time");
		register_setting("custom-ratings-settings", "wpcr_use_own_css");
		register_setting("custom-ratings-settings", "wpcr_hide_on_front_page");
		register_setting("custom-ratings-settings", "wpcr_hide_on_home_page");
		register_setting("custom-ratings-settings", "wpcr_hide_on_search_page");
		register_setting("custom-ratings-settings", "wpcr_hide_on_archive_page");

		// Register visual settings
		register_setting("custom-ratings-settings", "wpcr_max_rating_object_width");
		register_setting("custom-ratings-settings", "wpcr_max_tally_image_width");
		register_setting("custom-ratings-settings", "wpcr_decimal_type");
		register_setting("custom-ratings-settings", "wpcr_show_vote_count");
		register_setting("custom-ratings-settings", "wpcr_hide_tally_if_no_votes");

		// Register text settings
		register_setting("custom-ratings-settings", "wpcr_intro_text");
		register_setting("custom-ratings-settings", "wpcr_loading_text");
		register_setting("custom-ratings-settings", "wpcr_error_text");
		register_setting("custom-ratings-settings", "wpcr_first_vote_text");
		register_setting("custom-ratings-settings", "wpcr_adding_vote_text");
		register_setting("custom-ratings-settings", "wpcr_thank_you_text");
		register_setting("custom-ratings-settings", "wpcr_report_text");
		
	}

}
