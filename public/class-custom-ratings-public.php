<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://en-ca.wordpress.org/plugins/custom-ratings
 * @since      1.0.0
 *
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/public
 * @author     Steve Puddick <steve.puddick@gmail.com>
 */
class Custom_Ratings_Public {

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
	 * @param    string    $custom_ratings       The name of the plugin.
	 * @param    string    $version    	The version of this plugin.
	 */
	public function __construct( $custom_ratings, $version ) {

		$this->custom_ratings = $custom_ratings;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->custom_ratings, plugin_dir_url( __FILE__ ) . 'css/custom-ratings-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), '4.5.0', 'all' );
	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		if (is_admin()){
			return;
		}

		wp_enqueue_script( 'ractive', plugin_dir_url( __FILE__ ) . 'js/ractive.js', array( 'jquery' ), plugin_dir_path( dirname( __FILE__ ) ) . 'public/js/ractive.js', false );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wpcr_vote_script', plugin_dir_url( __FILE__ ) . 'js/custom-ratings-public.js', array( 'jquery', 'ractive' ), plugin_dir_path( dirname( __FILE__ ) ) . 'public/js/custom-ratings-public.js', false );
		
		$decimal_sep = get_option('wpcr_decimal_type');
		if (!$decimal_sep) {
			$decimal_sep = 'point';
		}
		$decimal_val = '.';
		switch($decimal_sep) {
			case 'point' :
				$decimal_val = '.';
				break;
			case 'comma' :
				$decimal_val = ',';
				break;
		}

		$rating_obj_src_url = Custom_Ratings::get_rating_object_image_src();
		list($rating_image_width, $rating_image_height) = getimagesize($rating_obj_src_url);

		$tally_image_width 	= get_option('wpcr_max_tally_image_width');
		if (!(get_option('wpcr_max_tally_image_width')) || !is_numeric(get_option('wpcr_max_tally_image_width')) ) {
			$tally_image_width = '35';
		}

		$show_vote_count 				= get_option('wpcr_show_vote_count');
		$hide_tally_if_no_votes = get_option('wpcr_hide_tally_if_no_votes');

		$report_string 	= __(get_option( 'wpcr_report_text'), 'custom-rating');
		$localized_data = array(
			'ajaxurl' 						=> admin_url( 'admin-ajax.php' ),
			'report_string' 			=> $report_string,
			'decimal_sep'					=> $decimal_val,
			'rating_image_w'			=> $rating_image_width,
			'rating_image_h'			=> $rating_image_height,
			'rating_image_set_w'	=> $tally_image_width,
			'show_vote_count' 		=> $show_vote_count,
			'hide_tally_if_no_votes' => $hide_tally_if_no_votes
		);
		wp_localize_script( 'wpcr_vote_script', 'wpcr_localized', $localized_data);        
		
		global $post;
		
	}


	/**
	*
	* Append Custom Ratings ratings display components to excerpt
	*
	* @since  1.0.0
	**/
	function append_ratings_to_excerpt($content){
		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/custom-ratings-display-variables-and-checks.php';
		if (!$show) {
			return $content;
		}
		if (!empty($selected_post_types) && 
			in_array($post_type_name, $selected_post_types) &&
			($excerpt_output_type == 'above' || $excerpt_output_type == 'below')
			 ) {
			
			require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-ratings-post-data.php';

			$output = '';
			ob_start();
			require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/custom-ratings-excerpt-public-display.php';
			$output = ob_get_clean();

			switch ($excerpt_output_type) {
				case 'above':
					$content = $output . $content;
					break;
				case 'below':
					$content = $content . $output;
					break;
			}
			
		}
		
		return $content;
	}	


	/**
	*
	* Append Custom Ratings ratings interface to post content
	*
	* @since  1.0.0
	**/
	function append_ratings_to_content($content){
		
		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/custom-ratings-display-variables-and-checks.php';
		if (!$show) {
			return $content;
		}
		if (current_filter() == 'the_content') {
			if (!empty($selected_post_types) && 
				in_array($post_type_name, $selected_post_types) &&
				($content_output_type == 'above' || $content_output_type == 'below')
				 ) {
				
				require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-ratings-post-data.php';
				
				$output = '';
				ob_start();
				require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/custom-ratings-post-public-display.php';
				$output = ob_get_clean();

				switch ($content_output_type) {
					case 'above':
						$content = $output . $content;
						break;
					case 'below':
						$content = $content . $output;
						break;
				}
			}
		}
		return $content;

	}


	/**
	*
	* Direct call to ouput Custom Ratings tally display
	*
	* @since  1.0.0
	**/
	public static function display() {
		global $post;
		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/custom-ratings-display-variables-and-checks.php';
		if (!$show) {
			return; 
		}

		$selected_post_types 	= get_option('wpcr_post_types');
		$post_type_name 			= get_post_type( $post );
		$output = '';
		if (!empty($selected_post_types) && 
			in_array($post_type_name, $selected_post_types)
			 ) {
			
			require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-ratings-post-data.php';
			if (empty($total_vote_count) || $total_vote_count == '0') {
				return;
			}

			$output = '';
			ob_start();
			require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/custom-ratings-excerpt-public-display.php';
			$output = ob_get_clean();
			
		}
		
		return $output;	
	}


	/**
	*
	* Direct call to ouput Custom Ratings vote interface
	*
	* @since  1.0.0
	**/
	public static function vote() {
		global $post;
		require plugin_dir_path( dirname( __FILE__ ) ) . 'public/custom-ratings-display-variables-and-checks.php';
		if (!$show) {
			return; 
		}

		$selected_post_types 	= get_option('wpcr_post_types');
		$post_type_name 			= get_post_type( $post );
		$star_type 						= get_option('wpcr_star_type');
		$output 							= '';

		if (!empty($selected_post_types) && 
			in_array($post_type_name, $selected_post_types))
			{
			
			require plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-ratings-post-data.php';
			
			$output = '';
			ob_start();
			require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/custom-ratings-post-public-display.php';
			$output = ob_get_clean();

		}
		
		return $output;		
	}


	/**
	*
	* Callback to place ractive js templates for display and vote components in footer
	*
	* @since  1.0.0
	**/
	function append_ractive_templates_to_footer() {
		$intro_text       			= get_option('wpcr_intro_text');
		$loading_text     			= get_option('wpcr_loading_text');
		$error_text       			= get_option('wpcr_error_text');
		$adding_vote_text 			= get_option('wpcr_adding_vote_text');
		$thank_you_text   			= get_option('wpcr_thank_you_text');
		$first_vote_text 				= get_option('wpcr_first_vote_text');
		$top_border 						= get_option('wpcr_top_border');
		$bottom_border 					= get_option('wpcr_bottom_border');
		$color 									= get_option('wpcr_color');
		$star_type 							= get_option('wpcr_star_type','cupcake');
		$max_image_width 				= get_option('wpcr_max_rating_object_width');
		$tally_image_width 			= get_option('wpcr_max_tally_image_width');
		$show_vote_count 				= get_option('wpcr_show_vote_count');
		$hide_tally_if_no_votes = get_option('wpcr_hide_tally_if_no_votes');

		

		if (!(get_option('wpcr_max_rating_object_width')) || !is_numeric(get_option('wpcr_max_rating_object_width')) ) {
			$max_image_width = '60';
		}
		if (!(get_option('wpcr_max_tally_image_width')) || !is_numeric(get_option('wpcr_max_tally_image_width')) ) {
			$tally_image_width = '35';
		}

		$top_border_style_string 				= '';
		$bottom_border_style_string 		= '';
		$circle_background_inline_css 	= '';

		if ($top_border == '1') {
			$top_border_style_string = 'border-bottom:1px solid ' . $color;
		}

		if ($bottom_border == '1') {
			$bottom_border_style_string = 'border-bottom:1px solid ' . $color;
		}

		if (!empty($color)){
			$circle_background_inline_css = 'background-color: ' . $color;
		}

		$star_img_url = Custom_Ratings::get_rating_object_image_src();

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/custom-ratings-public-ractive-template.php';
	}


}
