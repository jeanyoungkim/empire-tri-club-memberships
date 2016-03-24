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


defined( 'ABSPATH' ) or exit; // Exit if accessed directly


/** 
 * Modifies the WooCommerce Memberships start / end date for newly purchased user memberships
 *   if a start / end date has been set
 * 
 * Renewals / re-purchases will use the membership length to extend the membership from the
 *   original expiration date.
 */
class ETC_Memberships {
	
	
	/** @var ETC_Memberships single instance of this plugin */
	protected static $instance;
	
	
	public function __construct() {
	
		// load the metabox
		add_action( 'plugins_loaded', array( $this, 'includes' ), 20 );
		
		// add start & end date when membership is created
		add_action( 'wc_memberships_user_membership_created', array( $this, 'modify_dates' ), 50, 2 );	
	
	}
	
	
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
	
	
	/** Plugin methods ***************************************/
	
	
	/**
	 * Load the membership plan metabox
	 *
	 * @since  1.1.0
	 */
	public function includes() {
		require_once( 'metabox.php' );
	}
	

	/**
	 * Modifies the start / end dates for memberships (if set) when purchased
	 *
	 * @since 1.1.0
	 * @param \WC_Memberships_Membership_Plan $plan
	 * @param array $args {
	 *		@type int $user_id user ID making the purchase
	 *		@type int $user_membership_id post ID of the created user membership
	 * 		@type bool $updating true if the membership is being updated, not created
	 * }
	 */
	public function modify_dates( $plan, $args ) {

		// bail if the membership is not new / being created
		// this allows renewals / extensions to process properly
		if ( ! empty( $args['is_update'] ) ) {
			return;
		}

		$user_membership = wc_memberships_get_user_membership( $args['user_membership_id'] );

    	$start_date = get_post_meta( $plan->get_id(), '_etc_memberships_start_date', true );
    	$end_date = get_post_meta( $plan->get_id(), '_etc_memberships_end_date', true );

		// only update post meta if these values are set
    	if ( ! empty( $start_date ) ) {
    		update_post_meta( $user_membership->get_id(), '_start_date', $start_date );
    	}
    
    	if ( ! empty( $end_date ) ) {
    		update_post_meta( $user_membership->get_id(), '_end_date', $end_date );
    	}
	}

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


// fire it up!
etc_memberships();