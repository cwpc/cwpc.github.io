<?php
/*
 * Suports ortext internal key linking
 * To add linking for an additional post_type, append it to $link_types array in function admin_linking_init() 
 * 
 * This plugin adapted code from Julian Appert's Link to Post plugin
 * https://wordpress.org/plugins/link-to-post/
*/

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );


class otx_linking{
		
	function __construct(){		
	
		// add ortext editing scripts only to editing pages		
		add_action('admin_print_scripts-post.php',array(&$this,'editing_init'));
		add_action('admin_print_scripts-post-new.php',array(&$this,'editing_init'));
		add_action( 'edit_form_advanced', array(&$this,'quicktags'));
		add_action( 'edit_page_form', array(&$this,'quicktags'));	
		
		// ortext admin options
		add_action('admin_init', array(&$this,'admin_linking_init'));				
	}

	// initialization and editing support
	function editing_init(){
		$ortext_linking_options = get_option( 'ortext_linking_options' );	
		$type = $ortext_linking_options['defaultab'];
		if(!$type) $type = 'post';	

		wp_enqueue_script('otx-make-link', plugins_url('js/otx-make-link.js', dirname(__FILE__)), array('jquery'), '', true);
		?>
		<script type="text/javascript">
		//<![CDATA[
		function otxPlainButton() {
			var content = getContentSelection(window);
			tb_show("<?php _e( 'Ortext linker', 'otx-build' ); ?>","<?php echo plugins_url('linking/otx-link-choices.php', dirname(__FILE__)); ?>?type=<?php echo $type; ?>&amp;tri="+content+"&amp;validate=1&amp;where=both&amp;category=-1&amp;TB_iframe=true",false);
		}
		//]]>
		</script>	
		<?php
		
		// add tinymce button (rich editing) if appropriate
		if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') == 'true') {
			add_filter( "mce_external_plugins", array(&$this,"add_tinymce_plugin") );
			add_filter( 'mce_buttons', array(&$this,'register_button') );	
			add_filter( 'wp_fullscreen_buttons', array(&$this,'register_fullscreen_button') );
		}		
	}
	
	function quicktags(){
		$buttonshtml = '<input type="button" class="ed_button" onclick="otxPlainButton(); return false;" title="' . __('ortext link','otx-build') . '" value="' . __('ortext link','otx-build') . '" />';
		?>
		<script type="text/javascript" charset="utf-8">
		// <![CDATA[
		   (function(){
			  if (typeof jQuery === 'undefined') {
				 return;
			  }
			  jQuery(document).ready(function(){
				 jQuery("#ed_toolbar").append('<?php echo $buttonshtml; ?>');
			  });
		   }());
		// ]]>
		</script>
		<?php
	}

	// secure_sql used in pop-up floating thick box
	public static function secure_sql($sql){
		return mysql_real_escape_string($sql);
	}	
	
	// tinymce-specific (rich text) editing support
	function add_tinymce_plugin($plugin_array) {
	   $plugin_array['otxlink'] =  plugins_url('tinymce/otx-editor-plugin.js', dirname(__FILE__));
	   return $plugin_array;
	}

	function register_button($buttons) {
	   array_push($buttons, "separator", "otx_link");
	   return $buttons;
	}	
	
	function register_fullscreen_button( $buttons ) {
		// add a separator
		$buttons[] = 'separator';

		// format: title, onclick, show in both editors
		$buttons['otx_link'] = array(
		// Title of the button
		'title' => __('otxlink.makeLink'),
		// Command to execute
		'onclick' => "tinyMCE.execCommand('otxMceAddLink');",
		// Show on visual AND html mode
		'both' => true
		);

		return $buttons;
	}
	
	
	// admin page
	function admin_linking_init(){	
		$ortext_linking_options = get_option( 'ortext_linking_options' );
		register_setting( 'ortext_settings_group', 'ortext_linking_options', array( &$this, 'ortext_linking_validate' ));
		add_settings_section('ortext_linking_section', 'Ortext linking settings', array( &$this, 'ortext_linking_section_fn' ), 'ortext_settings' );
		add_settings_field( 'ortext_defaultab_field', 'Default linking group', array( &$this, 'input_defaultab_fn'), 'ortext_settings', 'ortext_linking_section', $ortext_linking_options );
/* hidden options; non-default values lead to non-functional legacy code
		add_settings_field( 'ortext_select_field', 'Search with selected text', array( &$this, 'input_select_fn'), 'ortext_settings', 'ortext_linking_section', $ortext_linking_options );		
		add_settings_field( 'ortext_nofollow_field', 'Add nofollow attribute to link', array( &$this, 'input_nofollow_fn'), 'ortext_settings', 'ortext_linking_section', $ortext_linking_options );	
		add_settings_field( 'ortext_shortcode_field', 'Use shortcode for link', array( &$this, 'input_shortcode_fn'), 'ortext_settings', 'ortext_linking_section', $ortext_linking_options );	
*/		
	}
	
	function ortext_linking_section_fn() {
		echo '';
	}
	
	function input_defaultab_fn( $options ) {
		$defaultab = $options['defaultab'];
		echo '<select id="defaultab" name="ortext_linking_options[defaultab]">';
		foreach ( $options['link_types'] as $link_type ) {
			if ( 'key' != $link_type ) $label = get_post_type_object( $link_type )->label;
			else $label = 'keys';
			echo '<option value="' . $link_type . '" ';
			if( $defaultab == $link_type) echo 'selected="selected"'; 
			echo '>' . __( $label, 'otx-build' ) . '</option>';
		}
		echo '</select>';
	}
	
	function input_select_fn( $options ) {
		$select = $options['select'];
		?>
		<input id='select' name='ortext_linking_options[select]' type='checkbox' value='on' <?php if($select == 'on') echo 'checked="checked"'; ?>/>
		<?php
	}

	function input_nofollow_fn( $options ) {
		$nofollow = $options['nofollow'];
		?>
		<input id='nofollow' name='ortext_linking_options[nofollow]' type='checkbox' value='on' <?php if($nofollow == 'on') echo 'checked="checked"'; ?>/>
		<?php
	}

	function input_shortcode_fn( $options ) {
		$shortcode = $options['shortcode'];
		?>
		<input id='shortcode' name='ortext_linking_options[shortcode]' type='checkbox' value='on' <?php if($shortcode == 'on') echo 'checked="checked"'; ?>/>
		<?php
	}	
	
	function ortext_linking_validate( $input ) {
		$ortext_linking_options = get_option( 'ortext_linking_options' );
		$link_types = $ortext_linking_options['link_types'];	
		if ( !in_array( $input['defaultab'], $link_types ) ) $ortext_linking_options['defaultab'] == 'post';
		else $ortext_linking_options['defaultab'] = $input['defaultab'];

/* hidden legacy options		
		if ( isset( $input['select'] ) && 'on' == $input['select'] ) $ortext_linking_options['select'] = 'on';
		else $ortext_linking_options['select'] = 'off';
		
		if ( isset( $input['nofollow'] ) && 'on' == $input['nofollow'] ) $ortext_linking_options['nofollow'] = 'on';
		else $ortext_linking_options['nofollow'] = 'off';

		if ( isset( $input['shortcode'] ) && 'on' == $input['shortcode'] ) $ortext_linking_options['shortcode'] = 'on';
		else $ortext_linking_options['shortcode'] = 'off';
*/		
		return $ortext_linking_options;
	}
 
}

new otx_linking();

?>