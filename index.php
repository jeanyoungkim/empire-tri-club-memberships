<?php
/**
 * Plugin Name: Empire Tri Club Memberships
 * Author: Jean Kim
 * Plugin URI: https://github.com/jeanyoungkim/empire-tri-club-memberships
 * Description: Allows membership start and end dates with WooCommerce Memberships
 * Version: 1.1.0
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

require( 'metabox.php' );

add_action( 'save_post', function($post_id) {
    $post = get_post($post_id);
defined( 'ABSPATH' ) or exit; // Exit if accessed directly


class ETC_Memberships {
	
	
	/** @var ETC_Memberships single instance of this plugin */
	protected static $instance;
	
	
	
	/** Helper methods ***************************************/
	
	
	/**
	 * Main ETC_Memberships Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.0.0
	 * @see etc_memberships()
	 * @return ETC_Memberships
 	*/
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	

    if ( $post->post_type != 'wc_user_membership' ) return;

    $user_membership = wc_memberships_get_user_membership( $post );
    $plan = get_post( $user_membership->get_plan_id() );
} // end \ETC_Memberships class


/**
 * Returns the One True Instance of ETC_Memberships
 *
 * @since 1.0.0
 * @return ETC_Memberships
 */
function etc_memberships() {
    return ETC_Memberships::instance();
}

    $start_date = get_post_meta( $plan->ID, '_etc_memberships_start_date', true );
    $end_date = get_post_meta( $plan->ID, '_etc_memberships_end_date', true );

    update_post_meta( $post->ID, '_start_date', $start_date );
    update_post_meta( $post->ID, '_end_date', $end_date );
}, 100, 2);// fire it up!
etc_memberships();