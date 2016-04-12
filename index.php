<?php
/**
 * Plugin Name: Empire Tri Club Memberships
 * Author: Jean Kim
 * Version: 1.0.0
 *
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

require( 'metabox.php' );

add_action( 'save_post', function($post_id) {
    $post = get_post($post_id);

    if ( $post->post_type != 'wc_user_membership' ) return;

    $user_membership = wc_memberships_get_user_membership( $post );
    $plan = get_post( $user_membership->get_plan_id() );

    if ( !get_post_meta( $plan->ID, '_etc_memberships_start_date', true ) ) return;

    $start_date = get_post_meta( $plan->ID, '_etc_memberships_start_date', true );
    $end_date = get_post_meta( $plan->ID, '_etc_memberships_end_date', true );

    update_post_meta( $post->ID, '_start_date', $start_date );
    update_post_meta( $post->ID, '_end_date', $end_date );
}, 100, 2);