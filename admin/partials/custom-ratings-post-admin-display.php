<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Custom_Ratings
 * @subpackage Custom_Ratings/admin/partials
 */
?>

<div >
	<?php if (!empty($root_post_id)) { ?>
		<p><?php printf( __( '<strong>%1$s</strong> vote(s) with an average of <strong>%2$s</strong>', 'custom-ratings' ), $total_vote_count, number_format(floatval($total_vote_avg), 2, $decimal_val, ' ') ); ?></p>
		
		<table class="wpcr__star-rating-table" >
			<tr>
				<th><?php _e('Rating Value', 'custom-ratings'); ?></th>
				<th><?php _e('Number of Ratings', 'custom-ratings'); ?></th>
			</tr>
			<?php
				for ($i = 0; $i < 5; $i++) {
					?>
						<tr>
							<td><?php echo ($i + 1) ?> <?php _e('star(s)', 'custom-ratings'); ?></td>
							<td><?php echo (isset($custom_rating_stars[$i])) ?  $custom_rating_stars[$i] : 0; ?></td>
						</tr>
					<?php
				}
			?>
		</table>
	<?php } else { ?>
		<p><?php _e('Custom Ratings requires a post in the default language to be created before functionality can be enabled.', 'custom-ratings'); ?></p>
	<?php } ?>
</div>