<?php

/*

require_once( '../../../../wp-blog-header.php' );

global $wpdb;

$args_cat = array(
  'orderby' => 'id',
  'order' => 'DESC'
  );
  
$categories = get_categories( $args_cat );  // used for category drop-down in pop-up thick boxes

$type = htmlspecialchars(sanitize_text_field($_REQUEST['type']), ENT_QUOTES);   // item type to searcch
$tri = htmlspecialchars(sanitize_text_field($_REQUEST['tri']), ENT_QUOTES);   // search text to filter items
$where = htmlspecialchars(sanitize_text_field($_REQUEST['where']), ENT_QUOTES);  // search item title/item text option
$category = htmlspecialchars(sanitize_text_field($_REQUEST['category']), ENT_QUOTES);  // category to filter search
$validate = htmlspecialchars(sanitize_text_field($_REQUEST['validate']), ENT_QUOTES);  // if contains "search" then after search form input

$ortext_linking_options = get_option( 'ortext_linking_options' );
$num_extracted = extract( $ortext_linking_options );  // defines $defaultab, $select, $nofollow, $shortcode, $link_types

if( strlen($type) == 0 ){
	$type = $defaultab;
	if(!$type) $type = 'post';
} 

$bFirstAndSelect = 0;
if( $select == 'on' && $validate == 1 && strlen($tri)>0) $bFirstAndSelect = 1;
	
function pages($nb,$nbpages,$page,$where = 'both',$tri = '',$category = -1,$type = 'post'){
	global $bFirstAndSelect;
	if (strlen(sanitize_text_field($_REQUEST['validate']))<2 && !$bFirstAndSelect) $tri = '';
	if(strlen($where)==0) $where = 'both';
	if(strlen($category)==0) $category = -1;
	echo '<p>';
	echo '<span class="results">';
	if($nb==1){
		echo '1 '; _e( 'result', 'otx-build' ); 
	}
	elseif($nb>1){
		echo $nb.' '; _e( 'results', 'otx-build' ); 
	}
	echo '</span>';
	if($nbpages > 1){
		for($i = 1;$i<=$nbpages;$i++){
			if($nbpages>=8){
				if($page > 4){
					if($i == 1){
						echo '<a href="otx-link-choices.php?type='.$type.'&validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">&lt;&lt;</a>&nbsp;&nbsp;';
						continue;
					}
					else if($i < $page -3){ continue;}
				}
				if($page < $nbpages - 3){
					if($i == $nbpages){
						echo '<a href="otx-link-choices.php?type='.$type.'&validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">&gt;&gt;</a>';
						continue;									
					}
					else if($i > $page +3){ continue; }
				}
			}
			if($i == $page){ $bold1 = '<strong>'; $bold2 = '</strong>'; }
			else { $bold1 = $bold2 = ''; }
			echo '<a href="otx-link-choices.php?type='.$type.'&validate=validate&where='.$where.'&tri='.$tri.'&category='.$category.'&page='.$i.'">'.$bold1.$i.$bold2.'</a>';
			if($i != $nbpages) echo '&nbsp;&nbsp;';
		}
	}
	echo '</p>';
}	
?>

<html>
<head>
<title>Ortext linker</title>
<link rel='stylesheet' href='../css/otx-link-choices.css' type='text/css' />

</head>
<body>

<div class="wrapper_quicktag">
<?php if ( 'key' != $type) { ?> <p><strong><?php _e( 'To insert the link, click on the item of your choice', 'otx-build' ); ?></strong></p><?php } ?>

<div class="tabs">
	<ul>
		<?php
		foreach ( $link_types as $link_type ) {	
			echo '<li id="' . $link_type . '_tab" ';
			if ( $link_type == $type ) echo 'class="current" '; 
			echo '><span><a href="otx-link-choices.php?type=' . $link_type . '&validate=' . $validate . '&where=' . $where . '&tri=' . $tri . '&category=' . $category . '">' . __( $link_type, 'otx-build' ) . '</a></span></li>';
		}
		?>	
	</ul>
</div>

<div class="panel_wrapper">
	<?php
	foreach ( $link_types as $link_type ) {	
		echo '<div id="' . $link_type . '_panel" class="panel';
		if ( $link_type == $type ) { echo ' current'; }
		echo '">';
		if ( $type == 'key' ) { include 'otx-link-keys.php'; }
		else { include 'otx-link-items.php'; }
		echo '</div>';
	}
	?>
</div>

</div>

<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>

<?php
wp_print_scripts('jquery');
wp_register_script('otx-make-link', plugins_url('js/otx-make-link.js', dirname(__FILE__)), array('jquery'), '', true);
wp_print_scripts('otx-make-link');
?>

</body>
</html>
*/