<?php

		global $post;
		$excerpt_output_type 	= get_option('wpcr_excerpt_output_type');
		$content_output_type 	= get_option('wpcr_content_output_type');
		$selected_post_types 	= get_option('wpcr_post_types');
		$post_type_name 			= get_post_type( $post );
		$only_on_main 				= get_option('wpcr_only_on_main_query');
		$only_in_loop 				= get_option('wpcr_only_in_loop');
		$use_own_css 					= get_option('wpcr_use_own_css');
		$css_wrapper_class		= 'wpcr_style';
		$hide_on_frontpage 		= get_option('wpcr_hide_on_front_page');
		$hide_on_homepage 		= get_option('wpcr_hide_on_home_page');
		$hide_on_archivepage 	= get_option('wpcr_hide_on_archive_page');
		$hide_on_searchpage 	= get_option('wpcr_hide_on_search_page');

		if ($use_own_css == '1') {
			$css_wrapper_class = '';
		}		

		$show = 1;
		if ( !is_main_query() && $only_on_main ) {
			$show = 0;
			return;
		}

		if ( !in_the_loop() && $only_in_loop ) {
			$show = 0;
			return;
		}

		if ( is_front_page() && $hide_on_frontpage ) {
			$show = 0;
			return;
		}

		if ( is_home() && $hide_on_homepage ) {
			$show = 0;
			return;
		}

		if ( is_archive() && $hide_on_archivepage ) {
			$show = 0;
			return;
		}

		if ( is_search() && $hide_on_searchpage ) {
			$show = 0;
			return;
		}


?>