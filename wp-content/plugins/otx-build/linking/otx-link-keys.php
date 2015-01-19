<?php
// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
?>

<fieldset class="filter">
	<legend><?php _e( 'Set key', 'otx-build' ); ?></legend>
	<form action="" id="fc">
		<input type="hidden" name="type" id="type" value="key" />
		<p>
			<label for="otxkey"><?php _e( 'Key value', 'otx-build' ); ?></label>
			<input type="text" name="otxkey" id="otxkey"/>
		</p>
		<?php echo '<input type="button" class="mceButton" value="' . __( 'Add key', 'otx-build') . '" onclick="otxsub = document.getElementById(\'otxkey\').value; return otxInsertLink(null,\'' . $nofollow .' \',\'' . $shortcode . '\', otxsub)" />';
		?>	
	</form>
</fieldset>