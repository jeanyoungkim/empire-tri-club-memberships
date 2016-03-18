<?php
function call_membershipsPlusMetabox() {
    new membershipsPlusMetabox();
}
 
if ( is_admin() ) {
    add_action( 'load-post.php',     'call_membershipsPlusMetabox' );
    add_action( 'load-post-new.php', 'call_membershipsPlusMetabox' );
}
 
/**
 * The Class.
 */
class membershipsPlusMetabox {
 
    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 5000 );
        add_action( 'save_post',      array( $this, 'save'         ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Adds datepicker and metabox script
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('memberships-plus-metabox', plugins_url( 'metabox.js', __FILE__ ), array( 'jquery-ui-datepicker' ) );
    }
 
    /**
     * Adds the meta box container.
     */
    public function add_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        $post_types = array( 'wc_membership_plan' );

        if ( in_array( $post_type, $post_types ) ) {
            add_meta_box(
                'some_meta_box_name',
                __( 'Some Meta Box Headline', 'textdomain' ),
                array( $this, 'render_meta_box_content' ),
                $post_type,
                'normal',
                'high'
            );
        }
    }
 
    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {
 
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */
 
        // Check if our nonce is set.
        if ( ! isset( $_POST['myplugin_inner_custom_box_nonce'] ) ) {
            return $post_id;
        }
 
        $nonce = $_POST['myplugin_inner_custom_box_nonce'];
 
        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box' ) ) {
            return $post_id;
        }
 
        /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
 
        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
 
        /* OK, it's safe for us to save the data now. */
 
        // Sanitize the user input.
        $start_date = sanitize_text_field( $_POST['etc_memberships_start_date'] );
        $end_date = sanitize_text_field( $_POST['etc_memberships_end_date'] );
 
        // Update the meta field.
        update_post_meta( $post_id, '_etc_memberships_start_date', $start_date );
        update_post_meta( $post_id, '_etc_memberships_end_date', $end_date );

    }
 
 
    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'myplugin_inner_custom_box', 'myplugin_inner_custom_box_nonce' );
 
        // Use get_post_meta to retrieve an existing value from the database.
        $start_date_value = get_post_meta( $post->ID, '_etc_memberships_start_date', true );
        $end_date_value = get_post_meta( $post->ID, '_etc_memberships_end_date', true );

 
        // Display the form, using the current value.
        ?>
        <label for="etc_memberships_start_date">
            <?php _e( 'Membership Start Date', 'textdomain' ); ?>
        </label>
        <input type="text" id="etc_memberships_start_date" class="date-picker" name="etc_memberships_start_date" value="<?php echo esc_attr( $start_date_value ); ?>" size="25" />
        <label for="etc_memberships_start_date">
            <?php _e( 'Membership End Date', 'textdomain' ); ?>
        </label>
        <input type="text" id="etc_memberships_end_date" class="date-picker" name="etc_memberships_end_date" value="<?php echo esc_attr( $end_date_value ); ?>" size="25" />
        <?php

    }
}