<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://en-ca.wordpress.org/plugins/custom-ratings
 * @since      1.0.0
 *
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/public/partials
 */
?>

<?php
		global $post;

		if (function_exists('icl_object_id')) {
			global $sitepress;
			$post_type 		= get_post_type( $post );
			$root_post_id = icl_object_id($post->ID, $post_type, false, $sitepress->get_default_language());
		} else {
			$root_post_id = $post->ID;
		}

		$star_type = get_option('wpcr_star_type','cupcake');

		$decimal_val = Custom_Ratings::get_decimal_value();

		$custom_rating_stars 				= get_post_meta( $root_post_id, '_wpcr_rating_stars', true );
		$custom_rating_star_value 	= get_post_meta( $root_post_id, '_wpcr_rating_stars_value', true );
		$total_vote_count 					= get_post_meta( $root_post_id, '_wpcr_rating_stars_count', true );
		$total_vote_value 					= get_post_meta( $root_post_id, '_wpcr_rating_stars_value', true );
		$total_vote_avg 						= get_post_meta( $root_post_id, '_wpcr_rating_stars_avg', true );
		$avg_rating 								= 0;
		$show_half_star 						= 0;

		if (empty($total_vote_count)) {
			$total_vote_count = 0;
		}
		if (empty($total_vote_value)) {
			$total_vote_value = 0;
		}
		if (empty($total_vote_avg)) {
			$total_vote_avg = 0;
		}

		if (!empty($custom_rating_stars)){

			$avg_rating 						= ($total_vote_count>0) ? round($total_vote_value / $total_vote_count, 2) : 0;
			$avg_rating_int_val 		= intval($avg_rating);
			$avg_rating_decimal_val = $avg_rating - $avg_rating_int_val;

			if ($avg_rating_decimal_val >= 0.5) {
				$show_half_star = 1;
			} else {
				$show_half_star = 0;
			}
		}
?>