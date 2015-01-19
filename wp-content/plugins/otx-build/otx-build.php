<?php
/*
 * Plugin Name: Ortext Build MODIFIED v2
 * Plugin URI: http://acrosswalls.org/datasets/make-ortext/
 * Description: Ortext builder tools, with chronological timelining, link inserter, and utility framework; be careful using on a publicly accessible site
 * Author: Communicating with Prisoners Collective
 * Author URI: http://acrosswalls.org/authors/
 * Version: 1.0
 * License: CCO 1.0 Universal
 * License URI: http://creativecommons.org/publicdomain/zero/1.0/legalcode
*/

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );


// class Otx_Admin adds a utility fire button to the Ortext settings page (conditionally based on OTX_UTL defined as TRUE)
// be careful with firing utilities; they can make major changes to the database
define('OTX_UTL', TRUE );
if ( OTX_UTL ) { include 'otx-util-run.php'; }	

// for security hardening, linking disabled below and all code for otx-link-choices.php (in linking folder) commented out
// include 'linking/otx-linking.php';	
include 'linking/otx-process-shortcode.php';

class Otx_Build {
	
	// ORTEXT ADMIN INITIALIZATION
	
	function __construct() {
		if ( ! is_admin() )
			return;
				
		// default options
		register_activation_hook( __FILE__, array( &$this, 'ortext_default_options' ));	
		
		// add ortext admin settings page	
		add_action('admin_menu', array(&$this,'admin_page'));	
		add_action('admin_init', array(&$this,'admin_dating_init'));
			
		// add date and time to admin list
		$this->dtal_setup();
	
		// ortext serial publication dating
		add_action('add_meta_boxes', array( &$this, 'add_dater_box' ));     
		add_action('draft_to_publish', array( &$this, 'ortext_dater' ));   // set date on publication 
		
		// otx-key defaults (see include)
		add_action('draft_to_publish', array( &$this, 'otx_key_default' ));
				
	}
	
	
	function ortext_default_options() {
		if ( !get_option( 'ortext_dating_options' ) ) :
			//  date_create('2010-01-01 0:00:00');  // only for PHP 5.3
			//	for earlier PHP, need mktime(hour,minute,second,month,day,year) and time step in seconds
			$ortext_dating_options = array( 'ortext_date' => mktime( 0,0,0,1,1,2012), 'time_step' => 3600 );
			add_option( 'ortext_dating_options', $ortext_dating_options );
		endif;
		if ( !get_option( 'ortext_linking_options' ) ) :
			$ortext_linking_options = array( 	'defaultab'		=> 'key',
												// legacy options; hidden because don't work, but default values needed
												'select'		=> 'off',
												'nofollow'		=> 'off',
												'shortcode'		=> 'on',  
											);	
			// key plus additional post types for ortext links	
			$ortext_linking_options[ 'link_types' ] = array( 'key', 'post', 'page', 'refs', 'notes', 'statistics', 'datasets' ); 	
			add_option( 'ortext_linking_options', $ortext_linking_options);	
		endif;
	}	
	
		
	// ORTEXT ADMIN SETTINGS PAGE
	
	function admin_page() {	
		add_options_page('Ortext Settings Page', 'Ortext', 'edit_posts', 'ortext_settings', array(&$this,'options_page'));
	}

/*
*  	additional options set in plugin otx-link
*	do_settings_sections param is the add_options_page slug (does sections for page)
*/	
	function options_page() { ?>
		<div class="ortext-admin">
		<h2><?php _e( 'Ortext Settings', 'otx-biuld' ); ?></h2>
		<form action="options.php" method="post">
		<?php settings_fields('ortext_settings_group'); ?>
		<?php do_settings_sections('ortext_settings'); ?>  
		<?php submit_button(); ?>
		</form>	
		<?php
		if ( OTX_UTL ) : 	
			?><h2><?php _e( 'Ortext Utility', 'otx-build' ); ?></h2><?php			
			global $otx_util;		
			if ( isset( $_POST['util_run_nonce'] ) && check_admin_referer( 'util_run', 'util_run_nonce' ) ) :
				$otx_util->util_fire();
				?><h2><?php _e( 'Ortext utility executed.', 'otx-build' ); ?></h2><?php
			endif; ?>		
			<form action="<?php echo esc_url( admin_url( 'options-general.php?page=ortext_settings' ) ); ?>" method="post">	
			<?php wp_nonce_field( 'util_run','util_run_nonce' ); ?>			
			<?php submit_button( 'Run utility (could be dangerous)' ); ?>
			</form>	
		<?php endif; ?>			
		</div> 
		<?php
	}
		
	function admin_dating_init(){
		register_setting( 'ortext_settings_group', 'ortext_dating_options', array( &$this, 'ortext_dating_validate' ));
		add_settings_section('ortext_dating_section', 'Ortext dating settings', array( &$this, 'ortext_dating_section_fn' ), 'ortext_settings' );
		add_settings_field( 'ortext_date_field', 'Base date-time', array( &$this, 'input_ortext_date_fn'), 'ortext_settings', 'ortext_dating_section' );
		add_settings_field( 'time_step_field', 'Time step', array( &$this, 'input_time_step_fn'), 'ortext_settings', 'ortext_dating_section' );		
	}

	function ortext_dating_section_fn() {
		echo '';
	}
	
	function input_ortext_date_fn() {
		$ortext_dating_options = get_option( 'ortext_dating_options' );
		$formated_date = strftime( '%Y-%m-%d %H:%M:%S', $ortext_dating_options['ortext_date'] );
		echo "<input id='ortext_date_field' name='ortext_dating_options[ortext_date]' size='30' type='text' value='$formated_date' />";
	}
	
	function input_time_step_fn() {
		$ortext_dating_options = get_option( 'ortext_dating_options' );
		$increment = '+' . $ortext_dating_options['time_step'] . ' seconds';
		echo "<input id='time_step_field' name='ortext_dating_options[time_step]' size='20' type='text' value='$increment' />";		
	}	
	
	function ortext_dating_validate($input) {
		$ortext_dating_options = get_option( 'ortext_dating_options' );
		$ortext_date_new = (int) strtotime( $input['ortext_date'], $ortext_dating_options['ortext_date'] );
		if ( $ortext_date_new ) {
			$ortext_dating_options['ortext_date'] = $ortext_date_new;
		}
		else {
			add_settings_error('ortext_date_field', 'ortext_date_error', 'Invalid base date-time', 'error' );
		}
		$time_step_new = (int) strtotime( $input['time_step'], $ortext_dating_options['ortext_date'] ); 
		if ( $time_step_new ) {
			$time_step_new -= $ortext_dating_options['ortext_date'];	
			$ortext_dating_options['time_step'] = $time_step_new;
		}
		else {
			add_settings_error('time_step_field', 'ortext_date_error', 'Invalid time step', 'error' );
		}
		return $ortext_dating_options;
	}


	// ADD DATE AND TIME TO ADMIN LIST AS WELL AS DETAIL TERMS
	
	function dtal_setup() {
		add_filter( 'manage_posts_columns', array( &$this, 'dtal_columns' ));
		add_action( 'manage_posts_custom_column', array( &$this, 'dtal_custom_column' ), 10, 2);
		add_filter( 'manage_edit-post_sortable_columns', array( &$this, 'dtal_column_register_sortable' ) );	
		add_filter( 'pre_get_posts', array( &$this,'dtal_column_orderby' ) );
	}
	
	function dtal_columns($defaults) {
    	$defaults['date-time'] = __( 'Date Time', 'otx-build' );
		return $defaults;
	}

	function dtal_custom_column($column_name, $post_id) {
		global $post;
		if( $column_name == 'date-time' ) echo get_the_time('Y/m/d, H:i:s') . '<br />' . $post->post_status;	
	}

	// Register the column as sortable
	function dtal_column_register_sortable( $columns ) {
		$columns['date-time'] = 'date-time';
		return $columns;
	}

	function dtal_column_orderby( $wp_query ) {
		if ( is_admin() ) {
			$wp_query->set( 'orderby', 'date-time' );
		}
	}


	// ORTEXT SERIAL PUBLICATION DATING 
	
	function add_dater_box() {
		global $post;
		if ( 'publish' != $post->post_status ) {
			add_meta_box( 'otx_dater_box', __( 'Ortext Serial Publication Dating', 'otx-build' ), array( &$this, 'dater_box_contents'), $post->post_type , 'side' , 'high' );
		}
	}

	function dater_box_contents() {

		// Use nonce for verification
		wp_nonce_field( 'spd_nonce_action', 'spd_nonce_name' );

		// ortext date-time and publication date-time checkbox
		$ortext_date = $this->get_ortext_date();
		echo '<label for="ortext_date">' . __( 'Ortext date ', 'otx-build' ) . $ortext_date . ' <br /></label>';
		echo '<input type="checkbox" id= "current_wp_date" name="current_wp_date" value="pub_date" size="25" /> Use current publication date';
	}

	function get_ortext_date() {
		global $post;
		if ( 'auto-draft' == $post->post_status ) {
			$ortext_dating_options = get_option( 'ortext_dating_options' );
			extract( $ortext_dating_options ); 
			if ( '' == $ortext_date ) return 'current date not set';
			if ( '' == $time_step ) return 'time step not set';
			$formated_ortext_date = strftime( '%Y-%m-%d %H:%M:%S', $ortext_date );
			add_post_meta( $post->ID , '_ortext-date' , $formated_ortext_date , true );
			$ortext_date += $time_step;  
			$ortext_dating_options['ortext_date'] = $ortext_date;
			remove_filter( 'sanitize_option_ortext_dating_options', array( &$this, 'ortext_dating_validate' ));
			update_option( 'ortext_dating_options', $ortext_dating_options );
		} 
		else {
			$formated_ortext_date = get_post_meta( $post->ID, '_ortext-date' , true );
		}
		return $formated_ortext_date;
	}

	function ortext_dater( $post ) {
		global $wpdb;

		// verify this came from our screen and with proper authorization,
		if ( empty($post) || !check_admin_referer( 'spd_nonce_action' , 'spd_nonce_name' ) ) return $post->ID;

		// if auto save routine, do nothing
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post->ID;
  
		// Check permissions
		if ( 'post' == $post->post_type && !current_user_can( 'edit_post' , $post->ID ) ) return $post->id;

		// OK, we're authenticated
		// write spd date unless checkbox indicates standard wp dating
		if ( !isset( $_POST['current_wp_date'] ) ) {
			$formated_ortext_date = get_post_meta( $post->ID, '_ortext-date' , true );
			$date_update = array( 'post_date' => $formated_ortext_date , 'post_date_gmt' => get_gmt_from_date($formated_ortext_date) );
			$wpdb->update( $wpdb->posts, $date_update , array( 'ID' => $post->ID ) );
		}
		delete_post_meta( $post->ID, '_ortext-date' );
	}

	
	// SET DEFAULT ORTEXT KEY

	function otx_key_default() {
		global $post;
		$otx_key = get_post_meta( $post->ID, 'otx-key', true);
		if ( '' == $otx_key ) {
			$otx_key = $post->post_type . '-' . $post->ID;
			update_post_meta( $post->ID, 'otx-key', $otx_key);
		}
	}

}

new Otx_Build();

?>