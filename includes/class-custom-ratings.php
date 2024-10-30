<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://en-ca.wordpress.org/plugins/custom-ratings
 * @since      1.0.0
 *
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/includes
 * @author     Steve Puddick <steve.puddick@gmail.com>
 */
class Custom_Ratings {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Custom_Ratings_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $custom_ratings    The string used to uniquely identify this plugin.
	 */
	protected $custom_ratings;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->custom_ratings = 'custom-ratings';
		$this->version = '1.5.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Custom_Ratings_Loader. Orchestrates the hooks of the plugin.
	 * - Custom_Ratings_i18n. Defines internationalization functionality.
	 * - Custom_Ratings_Admin. Defines all hooks for the admin area.
	 * - Custom_Ratings_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-custom-ratings-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-custom-ratings-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-custom-ratings-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-custom-ratings-public.php';

		$this->loader = new Custom_Ratings_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Custom_Ratings_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Custom_Ratings_i18n();
		$plugin_i18n->set_domain( $this->get_custom_ratings() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Custom_Ratings_Admin( $this->get_custom_ratings(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', 							$plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', 							$plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'add_meta_boxes', 											$plugin_admin, 'admin_single_add_meta_box' );
		$this->loader->add_action( 'init', 																$plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_wpcr_add_vote', 							$plugin_admin, 'wpcr_add_vote' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpcr_add_vote', 				$plugin_admin, 'wpcr_add_vote' );
		$this->loader->add_action( 'wp_ajax_wpcr_get_vote_counts', 				$plugin_admin, 'wpcr_get_vote_counts' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpcr_get_vote_counts', $plugin_admin, 'wpcr_get_vote_counts' );
		$this->loader->add_action( 'admin_menu', 													$plugin_admin, 'options_menu' );
		$this->loader->add_action( 'admin_init', 													$plugin_admin, 'display_theme_panel_fields' );
		$this->loader->add_action( 'pre_get_posts', 											$plugin_admin, 'wpcr_orderby' );
		$this->loader->add_action( 'save_post', 													$plugin_admin, 'wpcr_enabled_post_save' );
		$this->loader->add_action( 'update_option_wpcr_post_types', 			$plugin_admin, 'enabled_post_type_change', 10, 2 );
		$this->loader->add_action( 'plugins_loaded', 											$plugin_admin, 'plugin_update_check' );

		$selected_post_types 	= get_option('wpcr_post_types');
		if (empty($selected_post_types)) {
			$selected_post_types = array();
		}
		$plugin_admin->add_post_listing_column($selected_post_types);

	}


	public static function get_decimal_value() {
		$decimal_sep = get_option('wpcr_decimal_type');
		if (empty($decimal_sep)) {
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

		return $decimal_val;
	}

	/**
	 *	Get rating image src of selected rating object
	 * 
	 * @since    1.5.0
	 */
	public static function get_rating_object_image_src() {
		$star_type = get_option('wpcr_star_type','cupcake');

		if ($star_type == 'custom') {
			$wpcr_image_upload_url = '';
			$wpcr_image_upload_alt = '';

			$img_obj = wp_get_attachment_image_src(get_option('wpcr_image_upload_id'), 'thumbnail' );
			if (!empty($img_obj)) {
				$star_img_url = $img_obj[0];
				$star_img_alt = get_post_meta(get_option('wpcr_image_upload_id'), '_wp_attachment_image_alt', true);    	
			}
			
		} else {
			switch ($star_type) {
				case 'cupcake':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/cupcake.gif';
					$star_img_alt = ''; 
					break;
				case 'money':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/cash.png';
					$star_img_alt = '';
					break;
				case 'cat':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/cat.gif';
					$star_img_alt = '';
					break;
				case 'carrot':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/carrot.png';
					$star_img_alt = '';
					break;
				case 'frog-weightlift':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/frog-weightlift.gif';
					$star_img_alt = '';
					break;
				case 'ron-burgundy':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/ron-burgundy.jpg';
					$star_img_alt = '';
					break;
				case 'star':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/star.png';
					$star_img_alt = '';
					break;
				case 'wine-glass':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/wine-glass.png';
					$star_img_alt = '';
					break;
				case 'heart':
					$star_img_url = plugin_dir_url(dirname( __FILE__ )) . 'public/images/heart.png';
					$star_img_alt = '';
					break;
			}
		}

		return $star_img_url;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Custom_Ratings_Public( $this->get_custom_ratings(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', 	$plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', 	$plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'the_content', 				$plugin_public, 'append_ratings_to_content', 99 );
		$this->loader->add_filter( 'get_the_excerpt', 		$plugin_public, 'append_ratings_to_excerpt', 99 );
		$this->loader->add_action( 'wp_footer', 					$plugin_public, 'append_ractive_templates_to_footer' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_custom_ratings() {
		return $this->custom_ratings;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Custom_Ratings_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
