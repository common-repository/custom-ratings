<?php

/**
 * Fired during plugin activation
 *
 * @link       https://en-ca.wordpress.org/plugins/custom-ratings
 * @since      1.0.0
 *
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/includes
 * @author     Steve Puddick <steve.puddick@gmail.com>
 */
class Custom_Ratings_Activator {

	/**
	 * Set initial text values for custom ratings text snippets 
	 *
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		add_option('wpcr_star_type', 						'cupcake');
		add_option('wpcr_intro_text', 					__('Rate This!', 'custom-ratings'));
		add_option('wpcr_loading_text', 				__('Loading...', 'custom-ratings'));
		add_option('wpcr_error_text', 					__('An error occured while trying to add your vote. Try again later.', 'custom-ratings'));
		add_option('wpcr_adding_vote_text', 		__('Adding your vote...', 'custom-ratings'));
		add_option('wpcr_thank_you_text', 			__('Thank you for voting!', 'custom-ratings'));
		add_option('wpcr_report_text', 					__('%TOTAL_VOTES% votes with an average of %AVG%.', 'custom-ratings'));
		add_option('wpcr_post_types', 					array('post'));
		add_option('wpcr_excerpt_output_type', 	'below');
		add_option('wpcr_content_output_type', 	'below');
		add_option('wpcr_first_vote_text', 			__('Be the first to vote!', 'custom-ratings'));
		add_option('wpcr_only_on_main_query', 	'1');
		add_option('wpcr_only_in_loop', 				'1');
		
		Custom_Ratings_Admin::set_initial_rating_values();

	}

}
